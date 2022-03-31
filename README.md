Charcoal ImageCompression
===============

[![License][badge-license]][charcoal-image-compression]
[![Latest Stable Version][badge-version]][charcoal-image-compression]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

A [Charcoal][charcoal-app] module to handle image compression through compression api providers


## Table of Contents

-   [Installation](#installation)
    -   [Dependencies](#dependencies)
-   [Service Provider](#service-provider)
    -   [Parameters](#parameters)
    -   [Services](#services)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [Development](#development)
    -  [API Documentation](#api-documentation)
    -  [Development Dependencies](#development-dependencies)
    -  [Coding Style](#coding-style)
-   [Credits](#credits)
-   [License](#license)



## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-image-compression
```



### Dependencies

#### Required

- [**PHP 7.4+**](https://php.net)
- [**tinify/tinify-php**](https://github.com/tinify/tinify-php)



#### PSR

-   [**PSR-3**][psr-3]: Common interface for logging libraries. Fulfilled by Monolog.
-   [**PSR-11**][psr-11]: Common interface for dependency containers. Fulfilled by Pimple.



## Service Provider

### Parameters

--TBD--



### Services

--TBD--



## Configuration

The configuration of the comporession module is done via the modules key of the project configuration.
Charcoal image is hooked to use the compression module automatically once configured.

```json
{
    "modules": {
        "charcoal/image-compression/image-compression": {
            "autocompress": true,
            "providers": [...]
        }
    }
}

```

## Providers

The **providers** key can be used to list and configured some providers that are tasked to bridge the gaps between Charcoal and the different apis.
Each providers defines their own options. Here's an example of a provider configuration for tinify provider.

```json
{
    "providers": [
        {
            "type": "tinify",
            "key": "sdkfjeiSADkd",
            "maxCompressions": 500
        }
    ]
}
```

Multiple providers can be used at the same time and will be chained one after the other so that if a provider as reached a limit or fails, the next one on the list will be used instead.

### List of available providers

| Provider                                                                  | Package                                                    | Feature                                                      | Stats                                                                                                                                                                                                                                                                                                                                       |
|:--------------------------------------------------------------------------|:-----------------------------------------------------------|:-------------------------------------------------------------|:--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
[Tinify](https://github.com/locomotivemtl/charcoal-image-compression/tree/master/src/Charcoal/ImageCompression/Provider/Tinify) | `locomotivemtl/charcoal-image-compression/tinify-provider` | Jpg, png <br> [Website](https://tinypng.com/developers) |   


## Usage

By default, if a configuration for a provider is defined in the module's configuration, Charcoal Image properties will compress the uploaded images on the image save callback.



### ImageCompressor

The compression module can also be used as a standalone module via the ImageCompressor class. A container service is provided to access it.

```php
// Fetch image conmpression from pimple container
$this->imageCompressor = $container['image-compressor'];

// The compress method is used to compress a source file path to a target path.
$this->imageCompressor->compress($source, $target)
```


## Development

To install the development environment:

```shell
$ composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
$ composer test
```



### API Documentation

-   The auto-generated `phpDocumentor` API documentation is available at:  
    [https://locomotivemtl.github.io/charcoal-image-compression/docs/master/](https://locomotivemtl.github.io/charcoal-image-compression/docs/master/)
-   The auto-generated `apigen` API documentation is available at:  
    [https://codedoc.pub/locomotivemtl/charcoal-image-compression/master/](https://codedoc.pub/locomotivemtl/charcoal-image-compression/master/index.html)



### Development Dependencies

-   [php-coveralls/php-coveralls][phpcov]
-   [phpunit/phpunit][phpunit]
-   [squizlabs/php_codesniffer][phpcs]



### Coding Style

The charcoal-image-compression module follows the Charcoal coding-style:

-   [_PSR-1_][psr-1]
-   [_PSR-2_][psr-2]
-   [_PSR-4_][psr-4], autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   [phpcs.xml.dist](phpcs.xml.dist) and [.editorconfig](.editorconfig) for coding standards.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.



## Credits

-   [Locomotive](https://locomotive.ca/)



## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.



[charcoal-image-compression]:  https://packagist.org/packages/locomotivemtl/charcoal-image-compression
[charcoal-app]:             https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-image-compression/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-image-compression
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-image-compression

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-image-compression.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
[psr-12]: https://www.php-fig.org/psr/psr-12/
