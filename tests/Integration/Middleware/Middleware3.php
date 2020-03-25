<?php declare(strict_types=1);

namespace Delvesoft\Tests\Integration\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChainItem;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware3 extends AbstractMiddlewareChainItem
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            [],
            "{$request->getBody()}3"
        );

        return $this->processNext($request, $handler);
    }
}