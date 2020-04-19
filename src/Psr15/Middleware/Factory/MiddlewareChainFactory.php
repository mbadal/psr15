<?php declare(strict_types=1);

namespace Delvesoft\Psr15\Middleware\Factory;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Delvesoft\Psr15\Middleware\Exception\CouldNotCreateChainException;

class MiddlewareChainFactory
{
    /**
     * @param AbstractMiddlewareChainItem[] $chainItems
     *
     * @return AbstractMiddlewareChainItem
     * @throws CouldNotCreateChainException
     */
    public static function createFromArray(array $chainItems): AbstractMiddlewareChainItem
    {
        if ($chainItems === []) {
            throw new CouldNotCreateChainException('Could not create middleware chain from an empty array');
        }

        $first    = null;
        $previous = null;
        foreach ($chainItems as $actual) {
            if (!($actual instanceof AbstractMiddlewareChainItem)) {
                throw new CouldNotCreateChainException(
                    sprintf('Chain item: [%s] is not a child of: [%s]', get_class($actual), AbstractMiddlewareChainItem::class)
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