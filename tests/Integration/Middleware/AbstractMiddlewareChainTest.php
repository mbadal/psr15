<?php declare(strict_types=1);

namespace Delvesoft\Tests\Integration\Middleware;

use Delvesoft\Psr15\Middleware\Factory\ChainFactory;
use Delvesoft\Psr15\RequestHandler\AbstractRequestHandler;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class AbstractMiddlewareChainTest extends TestCase
{
    public function testCanProcess()
    {
        $request = new ServerRequest('GET', '/test', [], '');
        $handler = AbstractRequestHandler::createFromCallable(
            function (ServerRequestInterface $request) {
                return new Response(
                    200,
                    [],
                    $request->getBody()
                );
            }
        );

        $middleware1 = new Middleware1();
        $middleware2 = new Middleware2();
        $middleware3 = new Middleware3();

        $chain = ChainFactory::createFromArray(
            [
                $middleware1,
                $middleware2,
                $middleware3,
            ]
        );

        $response = $chain->process($request, $handler);
        $this->assertEquals('123', $response->getBody());
    }
}