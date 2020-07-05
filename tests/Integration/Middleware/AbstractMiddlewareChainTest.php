<?php

declare(strict_types=1);

namespace Delvesoft\Tests\Integration\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Delvesoft\Psr15\RequestHandler\AbstractRequestHandler;
use Mockery;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class AbstractMiddlewareChainTest extends TestCase
{
    public function testCanProcess()
    {
        $request = new ServerRequest('GET', '/test', [], '');
        $handler = AbstractRequestHandler::createFromCallable(
            function (ServerRequestInterface $request) {
                return new Response(
                    200,
                    [],
                    $request->getBody()
                );
            }
        );

        $factory     = new Psr17Factory();
        $middleware1 = new Middleware1($factory, $factory);
        $middleware2 = new Middleware2($factory, $factory);
        $middleware3 = new Middleware3($factory, $factory);

        $chain = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        $response = $chain->process($request, $handler);
        $this->assertEquals('123', $response->getBody());
    }

    public function testCanListChainedClassNames()
    {
        $factory     = new Psr17Factory();
        $middleware1 = new Middleware1($factory, $factory);
        $middleware2 = new Middleware2($factory, $factory);
        $middleware3 = new Middleware3($factory, $factory);

        $chain = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        $classNamesList    = $chain->listChainClassNames();
        $classesToVerified = [
            0 => $middleware1,
            1 => $middleware2,
            2 => $middleware3,
        ];

        foreach ($classNamesList as $index => $className) {
            $this->assertEquals(
                get_class($classesToVerified[$index]),
                $className
            );
        }
    }

    public function testCanAppend()
    {
        $factory     = new Psr17Factory();
        $middleware1 = new Middleware1($factory, $factory);
        $middleware2 = new Middleware2($factory, $factory);
        $middleware3 = new Middleware3($factory, $factory);

        $this->assertFalse($middleware1->hasNext());
        $middleware1->append($middleware2);
        $this->assertTrue($middleware1->hasNext());
        $this->assertEquals([Middleware1::class, Middleware2::class], $middleware1->listChainClassNames());

        $middleware1->append($middleware3);
        $this->assertTrue($middleware1->hasNext());
        $this->assertEquals([Middleware1::class, Middleware2::class, Middleware3::class], $middleware1->listChainClassNames());
    }

    public function testCanSetNext()
    {
        $factory     = new Psr17Factory();
        $middleware1 = new Middleware1($factory, $factory);
        $middleware2 = new Middleware2($factory, $factory);
        $middleware3 = new Middleware3($factory, $factory);

        $this->assertFalse($middleware1->hasNext());
        $middleware1->setNext($middleware2);
        $this->assertTrue($middleware1->hasNext());
        $this->assertEquals([Middleware1::class, Middleware2::class], $middleware1->listChainClassNames());

        $middleware1->setNext($middleware3);
        $this->assertTrue($middleware1->hasNext());
        $this->assertEquals([Middleware1::class, Middleware3::class], $middleware1->listChainClassNames());
    }

    public function testCanCreateServerRequest()
    {
        $reflectionClass = new ReflectionClass(AbstractMiddlewareChainItem::class);
        $method          = $reflectionClass->getMethod('createServerRequest');
        $method->setAccessible(true);

        $factory = new Psr17Factory();

        $httpMethod            = 'GET';
        $uri                   = new Uri('/test');
        $serverParams          = [
            'HTTP_REFERER' => '/start'
        ];
        $headers               = [
            'Content-Type' => [
                'application/json'
            ]
        ];
        $expectedServerRequest = new ServerRequest($httpMethod, $uri, [], null, '1.1', $serverParams);

        /** @var MockInterface|Psr17Factory $serverRequestFactory */
        $serverRequestFactory = Mockery::mock($factory, ServerRequestFactoryInterface::class);
        $serverRequestFactory
            ->shouldReceive('createServerRequest')
            ->once()
            ->andReturn(
                $expectedServerRequest
            );

        $instance = new Middleware1(
            $serverRequestFactory,
            $factory
        );

        foreach ($headers as $headerKey => $headerValue) {
            $expectedServerRequest = $expectedServerRequest->withHeader($headerKey, $headerValue);
        }

        /** @var ServerRequest $returnedServerRequest */
        $returnedServerRequest = $method->invoke($instance, $httpMethod, $uri, $serverParams, $headers);
        $this->assertEquals($expectedServerRequest->getMethod(), $returnedServerRequest->getMethod());
        $this->assertEquals($expectedServerRequest->getUri(), $returnedServerRequest->getUri());
        $this->assertEquals($expectedServerRequest->getServerParams(), $returnedServerRequest->getServerParams());
        $this->assertEquals($expectedServerRequest->getHeaders(), $returnedServerRequest->getHeaders());
    }

    public function testCanCreateResponse()
    {
        $reflectionClass = new ReflectionClass(AbstractMiddlewareChainItem::class);
        $method          = $reflectionClass->getMethod('createResponse');
        $method->setAccessible(true);

        $code             = 200;
        $reasonPhrase     = 'OK';
        $headers          = [
            'Content-Type' => [
                'application/json'
            ]
        ];
        $expectedResponse = new Response($code, [], null, '1.1', $reasonPhrase);
        $factory          = new Psr17Factory();

        /** @var MockInterface|Psr17Factory $responseFactory */
        $responseFactory = Mockery::mock($factory, ResponseFactoryInterface::class);
        $responseFactory
            ->shouldReceive('createResponse')
            ->once()
            ->andReturn(
                $expectedResponse
            );

        $instance = new Middleware1(
            $factory,
            $responseFactory

        );

        foreach ($headers as $headerKey => $headerValue) {
            $expectedResponse = $expectedResponse->withHeader($headerKey, $headerValue);
        }

        /** @var Response $returnedResponse */
        $returnedResponse = $method->invoke($instance, $code, $reasonPhrase, $headers);
        $this->assertEquals($expectedResponse->getStatusCode(), $returnedResponse->getStatusCode());
        $this->assertEquals($expectedResponse->getReasonPhrase(), $returnedResponse->getReasonPhrase());
        $this->assertEquals($expectedResponse->getHeaders(), $returnedResponse->getHeaders());
    }

    public function textCanTellWhetherHasSuccessorMiddleware()
    {
        $factory          = new Psr17Factory();
        $middleware = new Middleware1(
            $factory,
            $factory
        );

        $this->assertFalse($middleware->hasNext());

        $middleware->setNext(new Middleware2($factory, $factory));
        $this->assertTrue($middleware->hasNext());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}