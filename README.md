# middlewares/encoder

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to encode the response body using any of `gzip`, `deflate`, `brotli` or `zstd` compression (where available) if the `Accept-Encoding` header is present and can be matched. The `Content-Encoding` header is added when the body has been compressed. This package is split into the following components:

* [CompressEncoder](#compressencoder)
* [BrotliCompressor](#brotlicompressor)
* [DeflateCompressor](#deflatecompressor)
* [GzipCompressor](#gzipcompressor)
* [DeflateCompressor](#deflatecompressor)
* (deprecated) [GzipEncoder](#gzipencoder-deprecated)
* (deprecated) [DeflateEncoder](#deflateencoder-deprecated)

You can use the component `ContentEncoding` in the [middlewares/negotiation](https://github.com/middlewares/negotiation#contentencoding) to negotiate the encoding to use.

## Requirements

* PHP >= 7.2
* A [PSR-7 HTTP Library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 Middleware Dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

### Optional Requirements

* [PHP Brotli Extension](https://github.com/kjdev/php-ext-brotli)
* [PHP ZStd Extension](https://github.com/kjdev/php-ext-zstd)

## Installation

This package is installable and autoloadable via Composer as [middlewares/encoder](https://packagist.org/packages/middlewares/encoder).

```sh
composer require middlewares/encoder
```

## Upgrading from v2.x or earlier

When upgrading its advised to switch from using the deprecated GzipEncoder and or DeflateEncoder directly, to using the 
CompressEncoder middleware.

```diff
Dispatcher::run([
...
- 	new Middlewares\GzipEncoder(),
- 	new Middlewares\DeflateEncoder(),
+ 	new Middlewares\CompressEncoder(),
...
]);
```

_This configuration will try ZStd, Brotli, GZip, and then Deflate, in that order, if available (php extensions are 
loaded for zstd and or brotli)._

## CompressEncoder

Compress the response body to matching `Accept-Encoding` format format using the first matching Compressor (by default 
`zstd`, `brotli`, `gzip` and `defalate` are tried, in that order, where php extentions are available) after which the 
`Content-Encoding` header will be added to the output.

**Note:** The response body is encoded only if the `Accept-Encoding` header contains an available compression type, the
Content-Type is considered compressible, and there is no `Content-Encoding` header.

```php
Dispatcher::run([
	new Middlewares\CompressEncoder(),
]);
```

#### Optional Parameters:

- You can provide a `Psr\Http\Message\StreamFactoryInterface` that will be used to create the response body. 
If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used a default.

```php
$streamFactory = new MyOwnStreamFactory();

$encoder = new Middlewares\CompressEncoder($streamFactory);
```

- You can also provide your own list of Compressors (that implement the `Middlewares\CompressorInterface`). For example:

```php
$encoder = new Middlewares\CompressEncoder(null, [
  new MyProject\LzmaCompressor(),
  new Middlewares\GZipCompressor($level = 9),
]);

```

### Only compress specific Content-Types

This option allows the overriding of the default patterns used to detect what resources are already compressed.

The default pattern detects the following mime types `text/*`, `application/json`, `image/svg+xml` and empty content
types as compressible. If the pattern begins with a forward slash `/` it is tested as a regular expression, otherwise
its is treated as a case-insensitive string comparison.

```php
Dispatcher::run([
	(new Middlewares\CompressEncoder())
            ->contentType(
                    '/^application\/pdf$/', // Regular Expression
                    'text/csv' // Text Pattern
            )
]);
```

---

### BrotliCompressor

The brotli compressor is used where the `Accept-Encoding` includes `br` and can be configured with a custom compression 
level, via a constructor parameter.

```php
$encoder = new Middlewares\CompressEncoder(null, [
  new Middlewares\BrotliCompressor($level = 1),
]);
```
### DeflateCompressor

The deflate compressor is used where the `Accept-Encoding` includes `deflate` and can be configured with a custom compression 
level, via a constructor parameter.

```php
$encoder = new Middlewares\CompressEncoder(null, [
  new Middlewares\DeflateCompressor($level = 1),
]);
```

### GzipCompressor

The gzip compressor is used where the `Accept-Encoding` includes `gzip` and can be configured with a custom compression 
level, via a constructor parameter.

```php
$encoder = new Middlewares\CompressEncoder(null, [
  new Middlewares\GzipCompressor($level = 1),
]);
```

### ZStdCompressor

The gzip compressor is used where the `Accept-Encoding` includes `zstd` and can be configured with a custom compression 
level, via a constructor parameter.

```php
$encoder = new Middlewares\CompressEncoder(null, [
  new Middlewares\ZStdCompressor($level = 1),
]);
```

## GzipEncoder (deprecated)

**Please note, this is provided for backward compatibility only**

Compress the response body to GZIP format using [gzencode](http://php.net/manual/en/function.gzencode.php) and add the header `Content-Encoding: gzip`.

**Note:** The response body is encoded only if the header contains the value `gzip` in the header `Accept-Encoding`.

```php
Dispatcher::run([
	new Middlewares\GzipEncoder(),
]);
```

Optionally, you can provide a `Psr\Http\Message\StreamFactoryInterface` as above. 

## DeflateEncoder (deprecated)

**Please note, this is provided for backward compatibility only**

Compress the response body to Deflate format using [gzdeflate](http://php.net/manual/en/function.gzdeflate.php) and add the header `Content-Encoding: deflate`.

**Note:** The response body is encoded only if the header contains the value `deflate` in the header `Accept-Encoding`.

```php
Dispatcher::run([
	new Middlewares\DeflateEncoder(),
]);
```

Optionally, you can provide a `Psr\Http\Message\StreamFactoryInterface` as above.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/encoder.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/encoder/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/encoder.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/encoder
[link-downloads]: https://packagist.org/packages/middlewares/encoder
