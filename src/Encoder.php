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

    /**
     * @var string[] List of regular expressions to test if content type is compressible
     */
    private $rxCompressable = ['/^(image\/svg\\+xml|text\/.*|application\/json)(;.*)?$/'];

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
            && $this->isCompressible($response)
        ) {
            $stream = $this->streamFactory->createStream($this->encode((string) $response->getBody()));
            $vary = array_filter(array_map('trim', explode(',', $response->getHeaderLine('Vary'))));
            if (!in_array('Accept-Encoding', $vary, true)) {
                $vars[] = 'Accept-Encoding';
            }
            return $response
                ->withHeader('Content-Encoding', $this->encoding)
                ->withHeader('Vary', implode(',', $vary))
                ->withBody($stream);
        }

        return $response;
    }

    /**
     * Encode the body content.
     */
    abstract protected function encode(string $content): string;

    /**
     * Sets the list of compressible content-type patterns. If pattern begins with '/' treat as regular expression
     *
     * @param  string[] $typePatterns List of patterns to compare to Content-Type
     * @return $this
     */
    public function contentType(string ...$typePatterns): self
    {
        $this->rxCompressable = $typePatterns;
        return $this;
    }

    private function isCompressible(ResponseInterface $response): bool
    {
        $contentType = $response->getHeaderLine('Content-Type') ?: 'text/html';
        foreach ($this->rxCompressable as $pattern) {
            if (strpos($pattern, '/') === 0) {
                if (preg_match($pattern, $contentType) === 1) {
                    return true;
                }
            } else {
                if (strcasecmp($pattern, $contentType) === 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
