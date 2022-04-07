Tinify Compression Provider 
===============

[![License][badge-license]][charcoal-image-compression]
[![Latest Stable Version][badge-version]][charcoal-image-compression]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

This is an image compression provider for the Tinify API.

## Configuration

The Tinify provider needs a Tinify **Api key**. The configuration can be project wide or scoped to the provider while instantiating.

In project conifg, to use with ImageCompression module :
```json
{
    "modules": {
        "charcoal/image-compression/image-compression": {
            "providers": [
                {
                    "type": "tinify",
                    "key": "9CnT59Tj3D22vzWBM5bf8krKWsstrN5e"
                }
            ]
        }
    }
}
```

When instantiating, passing the key in the provider constructor :
```php
use Charcoal\ImageCompression\Provider\Tinify\TinifyProvider;

$provider = new TinifyProvider([
    'key' => "9CnT59Tj3D22vzWBM5bf8krKWsstrN5e"
]);
```


## Usage

```php
use Charcoal\ImageCompression\Provider\Tinify\TinifyProvider;

$provider = new TinifyProvider([
    'key' => "9CnT59Tj3D22vzWBM5bf8krKWsstrN5e"
]);

// get the total compressions count for the current month
$provider->compressionCount();

// compress a file
$provider->compress($source, $target);
```
Every API requests need an API key. Tinify supports a free tier which allows for 500 compressions per month.

## Installation

--TBD--


[charcoal-image-compression]:  https://packagist.org/packages/locomotivemtl/charcoal-image-compression

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-image-compression/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-image-compression
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-image-compression

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-image-compression.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-image-compression.svg?style=flat-square
