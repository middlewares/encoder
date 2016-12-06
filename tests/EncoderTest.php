<?php

namespace Middlewares\Tests;

use Middlewares\GzipEncoder;
use Middlewares\DeflateEncoder;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testGzipEncoder()
    {
        $request = Factory::createServerRequest()->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = Dispatcher::run([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('gzip', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzencode('Hello world'), (string) $response->getBody());
    }

    public function testDeflateEncoder()
    {
        $request = Factory::createServerRequest()->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = Dispatcher::run([
            new GzipEncoder(),
            new DeflateEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('deflate', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzdeflate('Hello world'), (string) $response->getBody());
    }

    public function testNoEncoder()
    {
        $request = Factory::createServerRequest()->withHeader('Accept-Encoding', 'foo');

        $response = Dispatcher::run([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertFalse($response->hasHeader('Content-Encoding'));
        $this->assertEquals('Hello world', (string) $response->getBody());
    }
}
