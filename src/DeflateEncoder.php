<?php

namespace Middlewares;

use Interop\Http\Middleware\ServerMiddlewareInterface;

class DeflateEncoder extends Encoder implements ServerMiddlewareInterface
{
    /**
     * @var string
     */
    protected $encoding = 'deflate';

    /**
     * {@inheritdoc}
     */
    protected function encode($content)
    {
        return gzdeflate($content);
    }
}
