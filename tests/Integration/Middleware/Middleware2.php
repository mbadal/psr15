<?php declare(strict_types=1);

namespace Delvesoft\Tests\Integration\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware2 extends AbstractMiddlewareChain
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            [],
            "{$request->getBody()}2"
        );

        return $this->processNext($request, $handler);
    }
}