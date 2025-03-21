# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.2.0] - 2025-03-21
### Added
- Support for PHP 8.4

## [2.1.1] - 2020-12-03
### Added
- Support for PHP 8.0

## [2.1.0] - 2020-01-24
### Added
- New option `contentType()` to set a list of content types that should be compressed [#3], [#4]

### Changed
- The middleware adds the header `Vary` [#3], [#4]

### Fixed
- Do not compress responses already compressed [#3], [#4]

## [2.0.0] - 2019-11-29
### Added
- Added `streamFactory` option to `__construct`

### Removed
- Option `streamFactory`. Use the argument in `__construct`.
- Support for PHP 7.0 and 7.1

## [1.1.0] - 2018-08-04
### Added
- PSR-17 support
- New option `streamFactory`

## [1.0.0] - 2018-01-27
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.5.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.4.0] - 2017-09-21
### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware#0.5`

## [0.3.2] - 2017-03-25
### Changed
- Updated Middlewares\Utils to use the `fixContentLength` helper

## [0.3.1] - 2017-03-22
### Fixed
- Fixed `Content-Length` response

## [0.3.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

## [0.2.0] - 2016-11-27
### Changed
- Updated to `http-interop/http-middleware#0.3`

## 0.1.0 - 2016-10-11
First version

[#3]: https://github.com/middlewares/encoder/issues/3
[#4]: https://github.com/middlewares/encoder/issues/4

[2.2.0]: https://github.com/middlewares/encoder/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/middlewares/encoder/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/middlewares/encoder/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/middlewares/encoder/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/middlewares/encoder/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/encoder/compare/v0.5.0...v1.0.0
[0.5.0]: https://github.com/middlewares/encoder/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/encoder/compare/v0.3.2...v0.4.0
[0.3.2]: https://github.com/middlewares/encoder/compare/v0.3.1...v0.3.2
[0.3.1]: https://github.com/middlewares/encoder/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/middlewares/encoder/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/encoder/compare/v0.1.0...v0.2.0
