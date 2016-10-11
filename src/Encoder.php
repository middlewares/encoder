<?php

namespace Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\DelegateInterface;

abstract class Encoder
{
    /**
     * @var string
     */
    protected $encoding;

    /**
     * Process a request and return a response.
     *
     * @param RequestInterface  $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);

        if (stripos($request->getHeaderLine('Accept-Encoding'), $this->encoding) !== false
            && !$response->hasHeader('Content-Encoding')
        ) {
            $stream = Utils\Factory::createStream();
            $stream->write($this->encode((string) $response->getBody()));

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
