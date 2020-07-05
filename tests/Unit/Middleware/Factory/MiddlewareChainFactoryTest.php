<?php

declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware\Factory;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Delvesoft\Psr15\Middleware\Exception\CouldNotCreateChainException;
use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Mockery;
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

        $times          = 1;
        $argumentsArray = [
            1 => $middleware2,
            2 => $middleware3,
        ];
        $middleware1
            ->shouldReceive('append')
            ->times(2)
            ->withArgs(
                function ($argument) use ($argumentsArray, &$times) {
                    return ($argument === $argumentsArray[$times++]);
                }
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}