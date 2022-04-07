Chain Compression Provider
===============

[![License][badge-license]][charcoal-image-compression]
[![Latest Stable Version][badge-version]][charcoal-image-compression]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

This is a special provider for [ImageCompression](../../../../../) package that allows to combine multiple providers in a single process.


## Usage

```php
use Charcoal\ImageCompression\Provider\Chain\ChainProvider;
use Charcoal\ImageCompression\Provider\Chain\ChainProvider;

$chainProvider = new ChainProvider([
    new TinifyProvider([...])
]);

$chainProvider->compress($source, $target);
```

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
