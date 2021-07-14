<?php
declare(strict_types = 1);

namespace Middlewares;

interface CompressorInterface
{
    /**
     * Return the name of the compression/encoding scheme, eg gzip, deflate
     *
     * @return string
     */
    public function name(): string;

    /**
     * Compress the data
     *
     * @param  string $input
     * @return string
     */
    public function compress(string $input): string;
}
