<?php
declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
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
        /** @var AbstractMiddlewareChainItem|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChainItem::class);
        $middleware2->shouldReceive('prepend')->once()->withArgs(
            [
                $middleware1
            ]
        );

        $middleware1->shouldReceive('setNext')->once()->withArgs([$middleware2]);

        $middleware2->prepend($middleware1);

        $this->assertTrue(true);
    }

    public function testCanAppend()
    {
        /** @var AbstractMiddlewareChainItem|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware3 */
        $middleware3 = Mockery::mock(AbstractMiddlewareChainItem::class);

        $middleware1
            ->shouldReceive('setNext')
            ->once()->withArgs(
                [
                    $middleware2
                ]
            );

        $middleware2
            ->shouldReceive('setNext')
            ->once()->withArgs(
                [
                    $middleware3
                ]
            );

        $chainStart = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware4 */
        $middleware4 = Mockery::mock(AbstractMiddlewareChainItem::class);

        $middleware1
            ->shouldReceive('append')
            ->once()
            ->withArgs(
                [
                    $middleware4
                ]
            );

        $chainStart->append($middleware4);
        $this->assertTrue(true);
    }

    public function testCanProcess()
    {
        $request = new ServerRequest('GET', '/test', [], '');

        /** @var AbstractRequestHandler|MockInterface $handler */
        $handler = Mockery::mock(AbstractRequestHandler::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware3 */
        $middleware3 = Mockery::mock(AbstractMiddlewareChainItem::class);

        $middleware1
            ->shouldReceive('setNext')
            ->once()->withArgs(
                [
                    $middleware2
                ]
            );

        $afterMiddleware1Request = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            [],
            '1'
        );

        $middleware1
            ->shouldReceive('process')
            ->once()
            ->withArgs(
                [
                    $request,
                    $handler
                ]
            );

        $middleware2
            ->shouldReceive('setNext')
            ->once()->withArgs(
                [
                    $middleware3
                ]
            );


        $middleware2
            ->shouldReceive('process')
            ->once()
            ->withArgs(
                [
                    $afterMiddleware1Request,
                    $handler
                ]
            );

        $afterMiddleware2Request = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            [],
            '12'
        );

        $middleware3
            ->shouldReceive('process')
            ->once()
            ->withArgs(
                [
                    $afterMiddleware2Request,
                    $handler
                ]
            );

        $afterMiddleware3Request = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            [],
            '123'
        );

        $response = new Response(
            200,
            [],
            '123'
        );
        $handler
            ->shouldReceive('handle')
            ->once()
            ->withArgs(
                [
                    $afterMiddleware3Request
                ]
            )->andReturn(
                $response
            );

        $chainStart = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        $chainStart->process($request, $handler);
        $this->assertTrue(true);
    }
}