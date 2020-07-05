<?php
declare(strict_types=1);

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

        $first    = current($chainItems);
        array_shift($chainItems);
        foreach ($chainItems as $item) {
            if (!($item instanceof AbstractMiddlewareChainItem)) {
                throw new CouldNotCreateChainException(
                    sprintf('Chain item: [%s] is not a child of: [%s]', get_class($item), AbstractMiddlewareChainItem::class)
                );
            }

            $first->append($item);
        }

        return $first;
    }
}