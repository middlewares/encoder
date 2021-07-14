<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

/***
 * @deprecated Replace with CompressEncoder
 */
class DeflateEncoder extends CompressEncoder implements MiddlewareInterface
{
    public function __construct(StreamFactoryInterface $streamFactory = null)
    {
        parent::__construct($streamFactory, [new DeflateCompressor()]);
    }
}
