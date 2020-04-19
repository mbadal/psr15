<?php declare(strict_types=1);

namespace Delvesoft\Psr15\RequestHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public static function createFromCallable(callable $handlerImplementation, ?callable $handlerResponseTransformation = null): self
    {
        return new class($handlerImplementation, $handlerResponseTransformation) extends AbstractRequestHandler {

            /** @var callable */
            private $handlerImplementation;

            /** @var callable|null */
            private $handlerResponseTransformation;

            public function __construct(callable $handlerImplementation, ?callable $handlerResponseTransformation)
            {
                $this->handlerImplementation         = $handlerImplementation;
                $this->handlerResponseTransformation = $handlerResponseTransformation;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $handlerResponse = call_user_func($this->handlerImplementation, $request);
                if ($this->handlerResponseTransformation === null) {
                    return $handlerResponse;
                }

                return call_user_func($this->handlerResponseTransformation, $handlerResponse);
            }
        };
    }

    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->responseFactory->createResponse($code, $reasonPhrase);
    }
}