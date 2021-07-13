<?php

namespace Middlewares;

final class BrotliCompressor implements CompressorInterface
{
    /** @var int Compression Level */
    private $level;

    /**
     * Brotli Compression Support (requires brotli php extension)
     *
     * @link https://github.com/kjdev/php-ext-brotli
     * @param int $level Brotli Compression Level, 11 is default
     */
    public function __construct(int $level = 11)
    {
        $this->level = $level;
    }

    public function name(): string
    {
        return 'br';
    }

    public function compress(string $input): string
    {
        $out = brotli_compress($input, $this->level);
        if($out === false) {
            throw new \RuntimeException('Error occurred while compressing output');
        }
        return $out;
    }
}
