<?php
declare(strict_types = 1);

namespace Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Middlewares\Utils\Helpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Encoder
{
    /**
     * @var string
     */
    protected $encoding;

    /**
     * Process a request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (stripos($request->getHeaderLine('Accept-Encoding'), $this->encoding) !== false
            && !$response->hasHeader('Content-Encoding')
        ) {
            $stream = Utils\Factory::createStream();
            $stream->write($this->encode((string) $response->getBody()));

            $response = $response
                ->withHeader('Content-Encoding', $this->encoding)
                ->withBody($stream);

            return Helpers::fixContentLength($response);
        }

        return $response;
    }

    /**
     * Encode the body content.
     */
    abstract protected function encode(string $content): string;
}
