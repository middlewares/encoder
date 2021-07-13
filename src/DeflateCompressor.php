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
        $out = gzdeflate($input, $this->level);
        if($out === false) {
            throw new \RuntimeException('Error occurred while compressing output');
        }
        return $out;
    }
}
