<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class Encoder
{
    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory = null)
    {
        $this->streamFactory = $streamFactory ?: Factory::getStreamFactory();
    }

    /**
     * Process a request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (stripos($request->getHeaderLine('Accept-Encoding'), $this->encoding) !== false
            && !$response->hasHeader('Content-Encoding')
        ) {
            $stream = $this->streamFactory->createStream($this->encode((string) $response->getBody()));

            return $response
                ->withHeader('Content-Encoding', $this->encoding)
                ->withoutHeader('Content-Length')
                ->withBody($stream);
        }

        return $response;
    }

    /**
     * Encode the body content.
     */
    abstract protected function encode(string $content): string;
}
