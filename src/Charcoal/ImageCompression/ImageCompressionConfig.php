<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 * Config: Image Compression
 */
class ImageCompressionConfig extends AbstractConfig
{
    /**
     * The model type that will serve to keep track of currently optimized images.
     *
     * @var class-string
     */
    private string $registryObject;

    /**
     * The batch compression settings.
     */
    private ?BatchCompressionConfig $batchConfig = null;

    /**
     * The default data is defined in a JSON file.
     *
     * @return array
     */
    public function defaults(): array
    {
        $baseDir = \rtrim(\realpath(__DIR__ . '/../../../'), '/');
        $confDir = $baseDir . '/config';

        return $this->loadFile($confDir . '/image-compression.json');
    }

    public function getRegistryObject(): string
    {
        return $this->registryObject;
    }

    /**
     * @param class-string $registryObject
     */
    public function setRegistryObject(string $registryObject): self
    {
        $this->registryObject = $registryObject;

        return $this;
    }

    public function getBatchConfig(): ?BatchCompressionConfig
    {
        return $this->batchConfig ??= new BatchCompressionConfig();
    }

    /**
     * @param mixed $config The batch configuration settings.
     *     Either a file path, an associative array, or a traversable object.
     */
    public function setBatchConfig($config): self
    {
        $this->getBatchConfig()->merge($config);

        return $this;
    }
}
