<?php declare(strict_types=1);

namespace Delvesoft\Psr15\MiddlewareChain\Factory;

use Delvesoft\Psr15\MiddlewareChain\AbstractMiddleware;
use InvalidArgumentException;

class ChainFactory
{
    /**
     * @param AbstractMiddleware[] $chainItems
     *
     * @return AbstractMiddleware
     */
    public static function createFromArray(array $chainItems): AbstractMiddleware
    {
        if ($chainItems === []) {
            throw new InvalidArgumentException('Could not create middleware chain from an empty array');
        }

        $first = array_shift($chainItems);
        if (!($first instanceof AbstractMiddleware)) {
            throw new InvalidArgumentException(sprintf('Chain item: [%s] is not instance child of: [AbstractMiddleware]', get_class($first)));
        }

        if ($chainItems === []) {
            return $first;
        }

        $previous = $first;
        while ($chainItems !== []) {
            $actual = array_shift($chainItems);
            if (!($actual instanceof AbstractMiddleware)) {
                throw new InvalidArgumentException(sprintf('Chain item: [%s] is not instance child of: [AbstractMiddleware]', get_class($actual)));
            }

            $previous->setNext($actual);
            $previous = $actual;
        }

        return $first;
    }
}