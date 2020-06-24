<?php

declare(strict_types=1);

namespace Delvesoft\Tests\Integration\Middleware;

use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Delvesoft\Psr15\RequestHandler\AbstractRequestHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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
}