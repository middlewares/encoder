<?php

namespace Middlewares;

use Interop\Http\Middleware\ServerMiddlewareInterface;

class GzipEncoder extends Encoder implements ServerMiddlewareInterface
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
