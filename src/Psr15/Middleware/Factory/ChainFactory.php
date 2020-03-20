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

        $first    = null;
        $previous = null;
        foreach ($chainItems as $actual) {
            if (!($actual instanceof AbstractMiddlewareChain)) {
                throw new InvalidArgumentException(
                    sprintf('Chain item: [%s] is not a child of: [%s]', get_class($actual), AbstractMiddlewareChain::class)
                );
            }

            if ($previous === null) {
                $previous = $actual;
                $first    = $actual;

                continue;
            }

            $previous->setNext($actual);
            $previous = $actual;
        }

        return $first;
    }
}