<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;
use Charcoal\ImageCompression\Contract\Model\RegistryInterface;

/**
 * Config: Image Compression
 */
class ImageCompressionConfig extends AbstractConfig
{
    /**
     * The model that'll serve to keep track of currently optimized images.
     *
     * @var string $registryObject
     */
    private string $registryObject;

    /**
     * The default data is defined in a JSON file.
     *
     * @return array
     */
    public function defaults(): array
    {
        $baseDir = rtrim(realpath(__DIR__.'/../../../'), '/');
        $confDir = $baseDir.'/config';

        return $this->loadFile($confDir.'/image-compression.json');
    }

    /**
     * @return string
     */
    public function getRegistryObject(): string
    {
        return $this->registryObject;
    }

    /**
     * @param string $registryObject RegistryObject for ImageCompressionConfig.
     * @return self
     */
    public function setRegistryObject(string $registryObject): self
    {
        $this->registryObject = $registryObject;

        return $this;
    }
}
