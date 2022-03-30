<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 * Config: Image Compression
 */
class ImageCompressionConfig extends AbstractConfig
{
    private array $providers;

    /**
     * The default data is defined in a JSON file.
     *
     * @return array
     */
    public function defaults()
    {
        $baseDir = rtrim(realpath(__DIR__.'/../../../'), '/');
        $confDir = $baseDir.'/config';

        $config = $this->loadFile($confDir.'/image-compression.json');

        return $config;
    }
}
