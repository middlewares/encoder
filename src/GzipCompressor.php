<?php
declare(strict_types = 1);

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
        $out = gzencode($input, $this->level);
        if ($out === false) {
            throw new \RuntimeException('Error occurred while compressing output');
        }
        return $out;
    }
}
