<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use Delvesoft\Psr15\RequestHandler\AbstractRequestHandler;
use Mockery;
use Mockery\MockInterface;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AbstractMiddlewareChainTest extends TestCase
{
    public function testCanPrepend()
    {
        $request = new ServerRequest('GET', '/test');
        /** @var AbstractRequestHandler|MockInterface $handler */
        $handler = Mockery::mock(AbstractRequestHandler::class);
        $handler
            ->shouldReceive('handle')
            ->once()
            ->withArgs(
                [
                    $request
                ]
            )
            ->andReturn(
                new Response(200, [], 'OK')
            );

        /** @var AbstractMiddlewareChain|MockInterface $toPrepend */
        $toPrepend = Mockery::mock(AbstractMiddlewareChain::class);

        $middleware = Mockery::mock(AbstractMiddlewareChain::class);
        $middleware->shouldReceive('prepend')->once()->withArgs(
            [
                $toPrepend
            ]
        );
        $middleware->shouldReceive('process')->once()->withArgs(
            [
                $request,
                $handler
            ]
        );

        $toPrepend->shouldReceive('setNext')->once()->withArgs([$middleware]);
        $toPrepend->shouldReceive('process')->once()->withArgs([$request, $handler]);

        $middleware->prepend($toPrepend);
        $toPrepend->process($request, $handler);
        
        $this->assertTrue(true);
    }
}