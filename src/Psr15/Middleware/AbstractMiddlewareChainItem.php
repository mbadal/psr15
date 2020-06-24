<?php

declare(strict_types=1);

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
        $last = $this;
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

    /**
     * @return string[]
     */
    public function listChainClassNames(): array
    {
        $names   = [
            get_class($this)
        ];
        $current = $this;
        while (($next = $current->getNext()) !== null) {
            $names[] = get_class($next);
            $current = $next;
        }

        return $names;
    }

    protected function processNext(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->next === null) {
            return $handler->handle($request);
        }

        return $this->next->process($request, $handler);
    }

    /**
     * @param string       $method
     * @param UriInterface $uri
     * @param string[]     $serverParams
     * @param string[][]   $headers
     *
     * @return ServerRequestInterface
     */
    protected function createServerRequest(string $method, UriInterface $uri, array $serverParams = [], array $headers = []): ServerRequestInterface
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest($method, $uri, $serverParams);
        foreach ($headers as $headerName => $headerValue) {
            $serverRequest->withHeader($headerName, $headerValue);
        }

        return $serverRequest;
    }

    /**
     * @param int        $code
     * @param string     $reasonPhrase
     * @param string[][] $headers
     *
     * @return ResponseInterface
     */
    protected function createResponse(int $code = 200, string $reasonPhrase = '', array $headers = []): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($code, $reasonPhrase);
        foreach ($headers as $headerName => $headerValue) {
            $response->withHeader($headerName, $headerValue);
        }

        return $response;
    }

    private function getNext(): ?AbstractMiddlewareChainItem
    {
        return $this->next;
    }
}