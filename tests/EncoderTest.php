<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\DeflateEncoder;
use Middlewares\GzipEncoder;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    public function testGzipEncoder()
    {
        $request = Factory::createServerRequest('GET', '/')->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = Dispatcher::run([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertEquals('gzip', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzencode('Hello world'), (string) $response->getBody());
    }

    public function testDeflateEncoder()
    {
        $request = Factory::createServerRequest('GET', '/')->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = Dispatcher::run([
            new GzipEncoder(),
            new DeflateEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertEquals('deflate', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzdeflate('Hello world'), (string) $response->getBody());
    }

    public function testNoEncoder()
    {
        $request = Factory::createServerRequest('GET', '/')->withHeader('Accept-Encoding', 'foo');

        $response = Dispatcher::run([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                echo 'Hello world';
            },
        ], $request);

        $this->assertFalse($response->hasHeader('Content-Encoding'));
        $this->assertFalse($response->hasHeader('Content-Length'));
        $this->assertEquals('Hello world', (string) $response->getBody());
    }
}
