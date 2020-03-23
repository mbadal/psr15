<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware\Factory;

use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Mockery;
use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ChainFactoryTest extends TestCase
{
    public function testCanCreate()
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

        $chain = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3
            ]
        );

        $this->assertEquals($chain, $middleware1);
    }
}