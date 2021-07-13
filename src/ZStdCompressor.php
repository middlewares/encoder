<?php

namespace Middlewares;

final class ZStdCompressor implements CompressorInterface
{
    /** @var int Compression Level */
    private $level;

    /**
     * ZStd Compression Support (requires zstd php extension)
     *
     * @link https://github.com/kjdev/php-ext-zstd
     * @param int $level ZStd Compression Level
     */
    public function __construct(int $level = ZSTD_COMPRESS_LEVEL_DEFAULT)
    {
        $this->level = $level;
    }

    public function name(): string
    {
        return 'zstd';
    }

    public function compress(string $input): string
    {
        return zstd_compress($input, $this->level);
    }
}
