<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 *  Batch Compression Config
 */
class BatchCompressionConfig extends AbstractConfig
{
    /**
     * @var array
     */
    protected array $fileExtensions = [];

    /**
     * @var string
     */
    protected string $basePath;

    /**
     * @return array
     */
    public function getFileExtensions(): array
    {
        return $this->fileExtensions;
    }

    /**
     * @param array $fileExtensions The file extensions to glob while batch compressing.
     * @return self
     */
    public function setFileExtensions(array $fileExtensions): self
    {
        $this->fileExtensions = $fileExtensions;

        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath The base path to glob in while batch compressing.
     * @return self
     */
    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }
}
