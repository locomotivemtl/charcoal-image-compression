<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 * Config: Image Compression
 */
class ImageCompressionConfig extends AbstractConfig
{
    /**
     * The default data is defined in a JSON file.
     *
     * @return array
     */
    public function defaults(): array
    {
        $baseDir = rtrim(realpath(__DIR__.'/../../../'), '/');
        $confDir = $baseDir.'/config';

        $config = $this->loadFile($confDir.'/image-compression.json');

        return $config;
    }
}
