<?php declare(strict_types=1);

namespace Delvesoft\Tests\Unit\Middleware;

use Delvesoft\Psr15\Middleware\AbstractMiddlewareChain;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware1 extends AbstractMiddlewareChain
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getBody();
        $newRequest = new ServerRequest(
            $request->getMethod(),
            $request->getHeaders(),
            "{$body}1"
        );

        return $this->processNext($newRequest, $handler);
    }
}