<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware\Factory;

use Delvesoft\Psr15\Middleware\Exception\CouldNotCreateChainException;
use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Mockery;
use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class MiddlewareChainFactoryTest extends TestCase
{
    public function testCanCreate()
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

        $chain = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3
            ]
        );

        $this->assertEquals($chain, $middleware1);
    }

    public function testWillThrowExceptionOnEmptyArray()
    {
        $this->expectException(CouldNotCreateChainException::class);
        MiddlewareChainFactory::createFromArray(
            []
        );
    }
}