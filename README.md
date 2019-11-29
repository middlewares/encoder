# middlewares/encoder

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to encode the response body to `gzip` or `deflate` if the `Accept-Encoding` header is present and adds the `Content-Encoding` header. This package is splitted into the following components:

* [GzipEncoder](#gzipencoder)
* [DeflateEncoder](#deflateencoder)

You can use the component `ContentEncoding` in the [middlewares/negotiation](https://github.com/middlewares/negotiation#contentencoding) to negotiate the encoding to use.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/encoder](https://packagist.org/packages/middlewares/encoder).

```sh
composer require middlewares/encoder
```

## Usage

```php
Dispatcher::run([
	new Middlewares\GzipEncoder(),
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

### GzipEncoder

Compress the response body to GZIP format using [gzencode](http://php.net/manual/en/function.gzencode.php) and add the header `Content-Encoding: gzip`.

**Note:** The response body is encoded only if the header contains the value `gzip` in the header `Accept-Encoding`.

```php
$encoder = new Middlewares\GzipEncoder();
```

Optionally, you can provide a `Psr\Http\Message\StreamFactoryInterface` that will be used to create the response body. If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$streamFactory = new MyOwnStreamFactory();

$encoder = new Middlewares\GzipEncoder($streamFactory);
```

### DeflateEncoder

Compress the response body to Deflate format using [gzdeflate](http://php.net/manual/en/function.gzdeflate.php) and add the header `Content-Encoding: deflate`.

**Note:** The response body is encoded only if the header contains the value `deflate` in the header `Accept-Encoding`.

```php
$encoder = new Middlewares\DeflateEncoder();
```

Optionally, you can provide a `Psr\Http\Message\StreamFactoryInterface` that will be used to create the response body. If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$streamFactory = new MyOwnStreamFactory();

$encoder = new Middlewares\DeflateEncoder($streamFactory);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/encoder.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/encoder/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/encoder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/encoder.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/encoder
[link-travis]: https://travis-ci.org/middlewares/encoder
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/encoder
[link-downloads]: https://packagist.org/packages/middlewares/encoder
