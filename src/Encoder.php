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

    private $rxCompressable = '_^(image/svg\+xml|text/.*|application/json|)(;.*)?$_';
    private $mimeCompressable = [];

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
     * Set a regex to test if content-type is compressible, and clears list of types
     *
     * @param  string $rx Regular Expression to test if content is compressable
     * @return $this
     */
    public function contentTypeRegex(string $rx): self
    {
        $this->rxCompressable = $rx;
        $this->mimeCompressable = [];
        return $this;
    }

    /**
     * Sets the list of compressible content-types, and clears the regex
     *
     * @param  string ...$types List of content types to compress
     * @return $this
     */
    public function contentTypeList(string ...$types): self
    {
        $this->mimeCompressable = $types;
        $this->rxCompressable = null;
        return $this;
    }

    private function isCompressible(ResponseInterface $response): bool
    {
        $contentType = $response->getHeaderLine('Content-Type') ?: 'text/html';
        if ($this->rxCompressable) {
            return preg_match($this->rxCompressable, $contentType) === 1;
        }
        return in_array($contentType, $this->mimeCompressable, true);
    }
}
