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
        $this->assertFalse($response->hasHeader('Content-Length'));
        $this->assertTrue($response->hasHeader('Vary'));
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
        $this->assertFalse($response->hasHeader('Content-Length'));
        $this->assertTrue($response->hasHeader('Vary'));
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
        $body = (string) $response->getBody();
        $this->assertEquals('Hello world', $body);
    }

    public function contentTypeList()
    {
        return [
            ['application/zip', '#ZIP', true],
            ['application/json', '#JSON', false],
            ['image/gif', '#GIF', true],
            ['image/jpeg', '#JPEG', true],
            ['image/png', '#PNG', true],
            ['image/svg+xml', '#SGV', false],
            ['image/webp', '#WEBP', true],
            ['text/csv', '#csv', false],
            ['text/html', '#HTML', false],
            ['text/html;charset=UTF-8', '#HTML', false],
            ['text/xml', '#XML', false],
        ];
    }

    /**
     * @dataProvider contentTypeList
     * @param mixed $contentType
     * @param mixed $body
     * @param mixed $isCompressed
     */
    public function testNoDoubleCompress($contentType, $body, $isCompressed)
    {
        $request = Factory::createServerRequest('GET', '/')->withHeader('Accept-Encoding', 'gzip,deflate');

        $response = Dispatcher::run([
            new DeflateEncoder(),
            new GzipEncoder(),
            function () use ($contentType, $body) {
                return self::makeResponse($contentType, $body);
            },
        ], $request);

        $this->assertEquals(
            !$isCompressed,
            $response->hasHeader('Content-Encoding'),
            'Content encoding should only be added to non compressed responses'
        );
        if ($isCompressed) {
            $this->assertEquals($body, (string) $response->getBody());
        } else {
            $this->assertEquals($body, gzdecode((string) $response->getBody()));
        }
    }

    public static function makeResponse($contentType, $body)
    {
        $res = Factory::createResponse(200)
            ->withHeader('Content-Type', $contentType);
        $res->getBody()->write($body);
        return $res;
    }
}
