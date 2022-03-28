Charcoal FooBar
===============

[![License][badge-license]][charcoal-contrib-foobar]
[![Latest Stable Version][badge-version]][charcoal-contrib-foobar]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

A [Charcoal][charcoal-app] service provider my cool feature.



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
$ composer require locomotivemtl/charcoal-contrib-foobar
```



### Dependencies

#### Required

-   [**PHP 7.1+**](https://php.net): _PHP 7.3+_ is recommended.



#### PSR

--TBD--



## Service Provider

### Parameters

--TBD--



### Services

--TBD--



## Configuration

--TBD--



## Usage

--TBD--



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
    [https://locomotivemtl.github.io/charcoal-contrib-foobar/docs/master/](https://locomotivemtl.github.io/charcoal-contrib-foobar/docs/master/)
-   The auto-generated `apigen` API documentation is available at:  
    [https://codedoc.pub/locomotivemtl/charcoal-contrib-foobar/master/](https://codedoc.pub/locomotivemtl/charcoal-contrib-foobar/master/index.html)



### Development Dependencies

-   [php-coveralls/php-coveralls][phpcov]
-   [phpunit/phpunit][phpunit]
-   [squizlabs/php_codesniffer][phpcs]



### Coding Style

The charcoal-contrib-foobar module follows the Charcoal coding-style:

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



[charcoal-contrib-foobar]:  https://packagist.org/packages/locomotivemtl/charcoal-contrib-foobar
[charcoal-app]:             https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-contrib-foobar/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-contrib-foobar
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-contrib-foobar

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-contrib-foobar.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-contrib-foobar.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-contrib-foobar.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-contrib-foobar.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-contrib-foobar.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
[psr-12]: https://www.php-fig.org/psr/psr-12/
