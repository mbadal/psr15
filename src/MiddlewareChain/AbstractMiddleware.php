<?php declare(strict_types=1);

namespace Delvesoft\MiddlewareChain;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /** @var AbstractMiddleware|null */
    private $next = null;

    public function setNext(AbstractMiddleware $next): self
    {
        $this->next = $next;

        return $this;
    }

    protected function processNext(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->next === null) {
            return $handler->handle($request);
        }

        return $this->next->process($request, $handler);
    }
}