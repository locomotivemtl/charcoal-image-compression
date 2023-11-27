Charcoal ImageCompression
===============

[![License][badge-license]][charcoal-image-compression]
[![Latest Stable Version][badge-version]][charcoal-image-compression]
[![Build Status][badge-travis]][dev-travis]
<!--[![Code Quality][badge-scrutinizer]][dev-scrutinizer]-->
<!--[![Coverage Status][badge-coveralls]][dev-coveralls]-->

A [Charcoal][charcoal-app] module to handle image compression through compression api providers


## Installation

The preferred (and only supported) method is with Composer:

```shell
composer require locomotivemtl/charcoal-image-compression
```



### Dependencies

#### Required

- [**PHP 7.4+**](https://php.net)
- [**tinify/tinify-php**](https://github.com/tinify/tinify-php)



#### PSR

-   [**PSR-3**][psr-3]: Common interface for logging libraries. Fulfilled by Monolog.
-   [**PSR-11**][psr-11]: Common interface for dependency containers. Fulfilled by Pimple.



## Service Provider

The following services are provided with the use of [_charcoal-image-compression_](https://github.com/locomotivemtl/charcoal-image-compression)

### Services

* [`image-compression`](src/Charcoal/ImageCompression/Service/ImageCompressionService.php) instance of `\Charcoal\ImageCompression\Service\ImageCompression`
* [`image-compressor`](src/Charcoal/ImageCompression/ImageCompressor.php) instance of `\Charcoal\ImageCompression\ImageCompressor`



## Configuration

The configuration of the comporession module is done via the modules key of the project configuration.
Charcoal image is hooked to use the compression module automatically once configured.

###### Example: Minimum configuration via `image_compression` definition

```jsonc
"image_compression": {
    "providers": [/* … */]
}
```

###### Example: Minimum configuration via `modules` definition

```jsonc
"modules": {
    "charcoal/image-compression/image-compression": {
        "providers": [/* … */]
    }
}
```

###### Example: Full configuration via `modules` definition with default options

```jsonc
"modules": {
    "charcoal/image-compression/image-compression": {
        "registryObject": "charcoal/image-compression/model/registry",
        "batchConfig": {
            "fileExtensions": [ "jpg", "jpeg", "png" ],
            "basePath": "uploads"
        },
        "providers": [/* … */]
    }
},
```

### Module Options

| Option             | Type       | Description                                                                                 | Default                                     |
|:-------------------|:-----------|---------------------------------------------------------------------------------------------|:--------------------------------------------|
| ~~`autoCompress`~~ | `bool`     | (_TODO_) Whether to compress files when they are saved in Charcoal.                         | `true`                                      |
| `registryObject`   | `string`   | The registry object to keep track of compression.                                           | `charcoal/image-compression/model/registry` |
| `batchConfig`      | `object`   | Options for the batch compression process.                                                  | `n/a`                                       |
| `fileExtension`    | `string[]` | List of extensions used with [`glob()`].                                                    | `[ "jpg", "jpeg", "png" ]`                  |
| `basePath`         | `string`   | The base path to glob from.                                                                 | `uploads`                                   |
| `providers`        | `array[]`  | List of providers with their options. Each provider have it's own set of options to define. | `[]`                                        |

## Providers

The **providers** key can be used to list and configured some providers that are tasked to bridge the gaps between
Charcoal and the different apis. Each providers defines their own options. Here's an example of a provider configuration
for tinify provider.

```json
{
    "providers": [
        {
            "type": "tinify",
            "key": "XXXXXX"
        }
    ]
}
```

Multiple providers can be used at the same time and will be chained one after the other so that if
a provider as reached a limit or fails, the next one on the list will be used instead.

### List of special providers

| Provider                                              | Package                                                   | Features           |
|:------------------------------------------------------|:----------------------------------------------------------|:-------------------|
| [Chain](src/Charcoal/ImageCompression/Provider/Chain) | `locomotivemtl/charcoal-image-compression/chain-provider` | Chaining providers |


### List of available providers

| Provider                                                | Package                                                    | Features                                               |
|:--------------------------------------------------------|:-----------------------------------------------------------|:-------------------------------------------------------|
| [Tinify](src/Charcoal/ImageCompression/Provider/Tinify) | `locomotivemtl/charcoal-image-compression/tinify-provider` | JPG, PNG, [Learn more](https://tinypng.com/developers) |


## Usage

TODO

~~By default, if provider is defined in the module's configuration,
Charcoal image properties will compress the uploaded images on the image save callback.
Must use the option `autoCompress` set to `true` which is the default behavior.~~

### Script

A script is provided to compress images on the server in a batch.

```shell
# Using default path from configuration
vendor/bin/charcoal admin/image-compression/batch-compress

# Using a custom path
vendor/bin/charcoal admin/image-compression/batch-compress --path my/custom/path
```


### Image Compressor

The compression module can also be used as a standalone module via the `ImageCompressor` class.
A container service is provided to access it.

```php
// Fetch image conmpression from pimple container
$this->imageCompressor = $container['image-compressor'];

// The compress method is used to compress a source file path to a target path.
$this->imageCompressor->compress($source, $target)
```

The `ImageCompressor` class will use the predefined module configuration and providers. For a custom implementation, instantiate the providers manually

```php
use Charcoal\ImageCompression\Provider\Tinify\TinifyProvider;

$provider = new TinifyProvider([...]);
$provider->compress($source, $target);

// Or use the special Chain Provider to chain providers together

use Charcoal\ImageCompression\Provider\Tinify\TinifyProvider;
use Charcoal\ImageCompression\Provider\Chain\ChainProvider;

$chainProvider = new ChainProvider([
    new TinifyProvider([...])
]);

$chainProvider->compress($source, $target);

```


## Development

To install the development environment:

```shell
composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
composer test
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
[charcoal-app]:                https://packagist.org/packages/locomotivemtl/charcoal-app

[glob]: https://www.php.net/function.glob

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-image-compression/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-image-compression
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-image-compression

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-image-compression?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-image-compression?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-image-compression?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-image-compression?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-image-compression?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
[psr-12]: https://www.php-fig.org/psr/psr-12/
