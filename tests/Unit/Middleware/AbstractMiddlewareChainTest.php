<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use Delvesoft\Psr15\Middleware\Factory\ChainFactory;
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
        /** @var AbstractMiddlewareChain|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChain::class);

        /** @var AbstractMiddlewareChain|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChain::class);
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
        /** @var AbstractMiddlewareChain|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChain::class);

        /** @var AbstractMiddlewareChain|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChain::class);

        /** @var AbstractMiddlewareChain|MockInterface $middleware3 */
        $middleware3 = Mockery::mock(AbstractMiddlewareChain::class);

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

        $chainStart = ChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        /** @var AbstractMiddlewareChain|MockInterface $middleware4 */
        $middleware4 = Mockery::mock(AbstractMiddlewareChain::class);

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
}