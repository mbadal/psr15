<?php declare(strict_types=1);

namespace Delvesoft\Psr15\Middleware\Factory;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use InvalidArgumentException;

class ChainFactory
{
    /**
     * @param AbstractMiddlewareChain[] $chainItems
     *
     * @return AbstractMiddlewareChain
     */
    public static function createFromArray(array $chainItems): AbstractMiddlewareChain
    {
        if ($chainItems === []) {
            throw new InvalidArgumentException('Could not create middleware chain from an empty array');
        }

        $first = array_shift($chainItems);
        if (!($first instanceof AbstractMiddlewareChain)) {
            throw new InvalidArgumentException(sprintf('Chain item: [%s] is not a child of: [%s]', get_class($first), AbstractMiddlewareChain::class));
        }

        if ($chainItems === []) {
            return $first;
        }

        $previous = $first;
        while ($chainItems !== []) {
            $actual = array_shift($chainItems);
            if (!($actual instanceof AbstractMiddlewareChain)) {
                throw new InvalidArgumentException(sprintf('Chain item: [%s] is not a child of: [%s]', get_class($actual), AbstractMiddlewareChain::class));
            }

            $previous->setNext($actual);
            $previous = $actual;
        }

        return $first;
    }
}