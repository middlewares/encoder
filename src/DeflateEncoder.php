<?php

namespace Middlewares;

use Interop\Http\ServerMiddleware\MiddlewareInterface;

class DeflateEncoder extends Encoder implements MiddlewareInterface
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
