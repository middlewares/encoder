<?php

namespace Middlewares;

final class GzipCompressor implements CompressorInterface
{
    /** @var int Compression Level */
    private $level;

    /**
     * GZIP Compression
     *
     * @param int $level GZIP Level, -1 for default compression level
     */
    public function __construct(int $level = -1)
    {
        $this->level = $level;
    }

    public function name(): string
    {
        return 'gzip';
    }

    public function compress(string $input): string
    {
        return gzencode($input, $this->level);
    }
}
