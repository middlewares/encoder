<?php

namespace Middlewares\Tests;

use Middlewares\GzipEncoder;
use Middlewares\DeflateEncoder;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use mindplay\middleman\Dispatcher;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testGzipEncoder()
    {
        $request = (new Request())->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = (new Dispatcher([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('gzip', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzencode('Hello world'), (string) $response->getBody());
    }

    public function testDeflateEncoder()
    {
        $request = (new Request())->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = (new Dispatcher([
            new GzipEncoder(),
            new DeflateEncoder(),
            function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('deflate', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzdeflate('Hello world'), (string) $response->getBody());
    }

    public function testNoEncoder()
    {
        $request = (new Request())->withHeader('Accept-Encoding', 'foo');

        $response = (new Dispatcher([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertFalse($response->hasHeader('Content-Encoding'));
        $this->assertEquals('Hello world', (string) $response->getBody());
    }
}
