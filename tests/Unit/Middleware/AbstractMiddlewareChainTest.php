<?php

declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Delvesoft\Psr15\Middleware\Factory\MiddlewareChainFactory;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AbstractMiddlewareChainTest extends TestCase
{
    public function testCanAppend()
    {
        /** @var AbstractMiddlewareChainItem|MockInterface $middleware1 */
        $middleware1 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware2 */
        $middleware2 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware3 */
        $middleware3 = Mockery::mock(AbstractMiddlewareChainItem::class);

        /** @var AbstractMiddlewareChainItem|MockInterface $middleware4 */
        $middleware4 = Mockery::mock(AbstractMiddlewareChainItem::class);

        $times          = 1;
        $argumentsArray = [
            1 => $middleware2,
            2 => $middleware3,
            3 => $middleware4,
        ];
        $middleware1
            ->shouldReceive('append')
            ->times(3)
            ->withArgs(
                function ($argument) use ($argumentsArray, &$times) {
                    return ($argument === $argumentsArray[$times++]);
                }
            );

        $chainStart = MiddlewareChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        $chainStart->append($middleware4);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}