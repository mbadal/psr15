<?php declare(strict_types=1);

namespace Delvesoft\Psr15\MiddlewareChain;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /** @var AbstractMiddleware|null */
    private $next = null;

    public function prepend(AbstractMiddleware $first): self
    {
        $first->setNext($this);

        return $first;
    }

    public function append(AbstractMiddleware $newLast): self
    {
        $last = null;
        for ($actual = $this; $actual !== null; $actual = $actual->getNext()) {
            $last = $actual;
        }

        $last->setNext($newLast);

        return $this;
    }

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

    private function getNext(): ?AbstractMiddleware
    {
        return $this->next;
    }
}