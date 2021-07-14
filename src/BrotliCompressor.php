<?php
declare(strict_types = 1);

namespace Middlewares;

final class BrotliCompressor implements CompressorInterface
{
    /** @var int Compression Level */
    private $level;

    /**
     * Brotli Compression Support (requires brotli php extension)
     *
     * @link https://github.com/kjdev/php-ext-brotli
     * @param int $level Brotli Compression Level, 5 is default (small than gzip, about as fast)
     */
    public function __construct(int $level = 5)
    {
        $this->level = $level;
    }

    public function name(): string
    {
        return 'br';
    }

    public function compress(string $input): string
    {
        $out = \brotli_compress($input, $this->level);
        if ($out === false) {
            throw new \RuntimeException('Error occurred while compressing output');
        }
        return $out;
    }
}