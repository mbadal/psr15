<?php declare(strict_types=1);

namespace Delvesoft\Psr15\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractMiddlewareChainItem implements MiddlewareInterface
{
    /** @var ServerRequestFactoryInterface */
    private $serverRequestFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var AbstractMiddlewareChainItem|null */
    private $next = null;

    public function __construct(ServerRequestFactoryInterface $serverRequestFactory, ResponseFactoryInterface $responseFactory)
    {
        $this->serverRequestFactory = $serverRequestFactory;
        $this->responseFactory      = $responseFactory;
    }

    public function prepend(AbstractMiddlewareChainItem $first): self
    {
        $first->setNext($this);

        return $first;
    }

    public function append(AbstractMiddlewareChainItem $newLast): self
    {
        $last = null;
        for ($actual = $this; $actual !== null; $actual = $actual->getNext()) {
            $last = $actual;
        }

        $last->setNext($newLast);

        return $this;
    }

    public function setNext(AbstractMiddlewareChainItem $next): self
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

    protected function createServerRequest(string $method, UriInterface $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->serverRequestFactory->createServerRequest($method, $uri, $serverParams);
    }

    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->responseFactory->createResponse($code, $reasonPhrase);
    }

    private function getNext(): ?AbstractMiddlewareChainItem
    {
        return $this->next;
    }
}