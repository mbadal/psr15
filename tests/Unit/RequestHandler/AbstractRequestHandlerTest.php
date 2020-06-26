<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\RequestHandler;

use Delvesoft\Psr15\RequestHandler\AbstractRequestHandler;
use Mockery;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AbstractRequestHandlerTest extends TestCase
{
    public function testCanWrapCallable()
    {
        $callable = Mockery::mock('handler');
        $callable
            ->shouldReceive('test')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    [],
                    'test'
                )
            );

        $handler = AbstractRequestHandler::createFromCallable([$callable, 'test']);
        $request = new ServerRequest('GET', '/test');

        $response = $handler->handle($request);
        $this->assertEquals('test', $response->getBody());
    }

    public function testCanTransformCallableInnerReturnValue()
    {
        $returnString = 'test';
        $callable     = Mockery::mock('handler');
        $callable
            ->shouldReceive('test')
            ->once()
            ->andReturn(
                $returnString
            );

        $transformer = Mockery::mock('transformer');
        $transformer
            ->shouldReceive('transform')
            ->once()
            ->andReturn(
                new Response(
                    200,
                    [],
                    $returnString
                )
            );

        $handler = AbstractRequestHandler::createFromCallable([$callable, 'test'], [$transformer, 'transform']);
        $request = new ServerRequest('GET', '/test');

        $response = $handler->handle($request);
        $this->assertEquals('test', $response->getBody());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}