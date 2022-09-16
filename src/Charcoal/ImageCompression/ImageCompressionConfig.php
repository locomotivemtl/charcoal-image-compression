<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 * Config: Image Compression
 */
class ImageCompressionConfig extends AbstractConfig
{
    private bool $autoCompress = true;
    private ?BatchCompressionConfig $batchConfig = null;

    /**
     * The model that'll serve to keep track of currently optimized images.
     */
    private string $registryObject;

    /**
     * The default data is defined in a JSON file.
     *
     * @return array
     */
    public function defaults(): array
    {
        $confDir = dirname(__DIR__, 3) . '/config';

        return $this->loadFile($confDir . '/image-compression.json');
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

    /**
     * @return BatchCompressionConfig|null
     */
    public function getBatchConfig(): ?BatchCompressionConfig
    {
        return $this->batchConfig;
    }

    /**
     * @param array $batchConfig The batch configuration as array.
     * @return self
     */
    public function setBatchConfig(array $batchConfig): self
    {
        $this->batchConfig = new BatchCompressionConfig($batchConfig);

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoCompress(): bool
    {
        return $this->autoCompress;
    }

    /**
     * @param bool $autoCompress AutoCompress for ImageCompressionConfig.
     * @return self
     */
    public function setAutoCompress(bool $autoCompress): self
    {
        $this->autoCompress = $autoCompress;

        return $this;
    }
}
