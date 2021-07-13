<?php

namespace Middlewares;

final class DeflateCompressor implements CompressorInterface
{
    /** @var int Compression Level */
    private $level;

    /**
     * Deflate Compression
     *
     * @param int $level Compression Level, -1 for default compression level
     */
    public function __construct(int $level = -1)
    {
        $this->level = $level;
    }

    public function name(): string
    {
        return 'deflate';
    }

    public function compress(string $input): string
    {
        return gzdeflate($input, $this->level);
    }
}
