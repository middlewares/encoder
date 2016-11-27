<?php

namespace Middlewares\Tests;

use Middlewares\GzipEncoder;
use Middlewares\DeflateEncoder;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\CallableMiddleware;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testGzipEncoder()
    {
        $request = (new ServerRequest())->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = (new Dispatcher([
            new DeflateEncoder(),
            new GzipEncoder(),
            new CallableMiddleware(function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            }),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('gzip', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzencode('Hello world'), (string) $response->getBody());
    }

    public function testDeflateEncoder()
    {
        $request = (new ServerRequest())->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = (new Dispatcher([
            new GzipEncoder(),
            new DeflateEncoder(),
            new CallableMiddleware(function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            }),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('deflate', $response->getHeaderLine('Content-Encoding'));
        $this->assertEquals(gzdeflate('Hello world'), (string) $response->getBody());
    }

    public function testNoEncoder()
    {
        $request = (new ServerRequest())->withHeader('Accept-Encoding', 'foo');

        $response = (new Dispatcher([
            new DeflateEncoder(),
            new GzipEncoder(),
            new CallableMiddleware(function () {
                $response = new Response();
                $response->getBody()->write('Hello world');

                return $response;
            }),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertFalse($response->hasHeader('Content-Encoding'));
        $this->assertEquals('Hello world', (string) $response->getBody());
    }
}
