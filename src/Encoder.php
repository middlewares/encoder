<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

abstract class Encoder
{
    /**
     * @var string
     */
    protected $encoding;

    /**
     * Process a request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);

        if (stripos($request->getHeaderLine('Accept-Encoding'), $this->encoding) !== false
            && !$response->hasHeader('Content-Encoding')
        ) {
            $stream = Utils\Factory::createStream();
            $stream->write($this->encode((string) $response->getBody()));

            if ($stream->getSize() !== null) {
                $response = $response->withHeader('Content-Length', (string) $stream->getSize());
            } else {
                $response = $response->withoutHeader('Content-Length');
            }

            return $response
                ->withHeader('Content-Encoding', $this->encoding)
                ->withBody($stream);
        }

        return $response;
    }

    /**
     * Encode the body content.
     *
     * @param string $content
     *
     * @return string
     */
    abstract protected function encode($content);
}
