<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CompressEncoder
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
    private $patterns = ['/^(image\/svg\\+xml|text\/.*|application\/json)(;.*)?$/'];

    /**
     * @var CompressorInterface[]
     */
    private $compressors;

    /**
     * CompressEncoder constructor.
     * @param StreamFactoryInterface|null $streamFactory
     * @param CompressorInterface[]|null  $compressors
     */
    public function __construct(StreamFactoryInterface $streamFactory = null, array $compressors = null)
    {
        $this->streamFactory = $streamFactory ?: Factory::getStreamFactory();
        $this->compressors = $compressors ?: $this->allAvailableCompressors();
    }

    /**
     * Process a request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $compressor = $this->getCompressor($request);
        if ($compressor
            && !$response->hasHeader('Content-Encoding')
            && $this->isCompressible($response)
        ) {
            $stream = $this->streamFactory->createStream($compressor->compress((string) $response->getBody()));
            $vary = array_filter(array_map('trim', explode(',', $response->getHeaderLine('Vary'))));

            if (!in_array('Accept-Encoding', $vary, true)) {
                $vary[] = 'Accept-Encoding';
            }

            return $response
                ->withHeader('Content-Encoding', $compressor->name())
                ->withHeader('Vary', implode(',', $vary))
                ->withBody($stream);
        }

        return $response;
    }

    /**
     * Sets the list of compressible content-type patterns.
     * If pattern begins with '/' treat as regular expression
     */
    public function contentType(string ...$patterns): self
    {
        $this->patterns = $patterns;
        return $this;
    }

    private function getCompressor(RequestInterface $request): ?CompressorInterface
    {
        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');
        foreach ($this->compressors as $comp) {
            if (stripos($acceptEncoding, $comp->name()) !== false) {
                return $comp;
            }
        }
        return null;
    }

    private function isCompressible(ResponseInterface $response): bool
    {
        $contentType = $response->getHeaderLine('Content-Type') ?: 'text/html';
        foreach ($this->patterns as $pattern) {
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

    /**
     * @return CompressorInterface[]
     */
    private function allAvailableCompressors(): array
    {
        $o = [];
        if (function_exists('zstd_compress')) {
            $o[] = new ZStdCompressor();
        }
        if (function_exists('brotli_compress')) {
            $o[] = new BrotliCompressor();
        }
        $o[] = new GzipCompressor();
        return $o;
    }
}
