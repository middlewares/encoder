<?php

namespace Middlewares;

use Interop\Http\Middleware\MiddlewareInterface;

class GzipEncoder extends Encoder implements MiddlewareInterface
{
    /**
     * @var string
     */
    protected $encoding = 'gzip';

    /**
     * {@inheritdoc}
     */
    protected function encode($content)
    {
        return gzencode($content);
    }
}
