<?php

namespace Charcoal\ImageCompression;

use Charcoal\Config\AbstractConfig;

/**
 * Config: Batch Compression
 */
class BatchCompressionConfig extends AbstractConfig
{
    /**
     * The file extensions to glob while batch compressing.
     *
     * @var list<string>
     */
    protected array $fileExtensions = [];

    /**
     * The base path to glob in while batch compressing.
     *
     * @var string
     */
    protected string $basePath;

    public function getFileExtensions(): array
    {
        return $this->fileExtensions;
    }

    /**
     * @param list<string> $fileExtensions
     */
    public function setFileExtensions(array $fileExtensions): self
    {
        $this->fileExtensions = $fileExtensions;

        return $this;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }
}
