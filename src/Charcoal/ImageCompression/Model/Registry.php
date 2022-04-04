<?php

namespace Charcoal\ImageCompression\Model;

use Charcoal\ImageCompression\Contract\Model\RegistryInterface;
use Charcoal\Model\AbstractModel;

/**
 * Class Registry
 *
 * A registry entry to keep track of compressed files and their data.
 * Prevents compressing the same files multiple times.
 */
class Registry extends AbstractModel implements RegistryInterface
{
    /**
     * @var integer|null
     */
    private ?int $size;

    /**
     * @var integer|null
     */
    private ?int $memorySaved;

    /**
     * @var integer|null
     */
    private ?int $originalSize;

    /**
     * @var string|null
     */
    private ?string $basename;

    /**
     * @var string|null
     */
    private ?string $filename;

    /**
     * @var string|null
     */
    private ?string $extension;

    /**
     * The file size.
     *
     * @return integer
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * @param integer|null $size Size for Registry.
     * @return self
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * The size of the file before compression.
     *
     * @return integer
     */
    public function originalSize(): int
    {
        return $this->originalSize;
    }

    /**
     * @param integer|null $originalSize OriginalSize for Registry.
     * @return self
     */
    public function setOriginalSize(?int $originalSize): self
    {
        $this->originalSize = $originalSize;

        return $this;
    }

    /**
     * The amount of memory saved for the file after compression.
     *
     * @return integer
     */
    public function memorySaved(): int
    {
        return $this->memorySaved;
    }

    /**
     * @param integer|null $memorySaved MemorySaved for Registry.
     * @return self
     */
    public function setMemorySaved(?int $memorySaved): self
    {
        $this->memorySaved = $memorySaved;

        return $this;
    }

    /**
     * @return string
     */
    public function basename(): string
    {
        return $this->basename;
    }

    /**
     * @param string|null $basename Basename for Registry.
     * @return self
     */
    public function setBasename(?string $basename): self
    {
        $this->basename = $basename;

        return $this;
    }

    /**
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename Filename for Registry.
     * @return self
     */
    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function extension(): string
    {
        return $this->extension;
    }

    /**
     * @param string|null $extension Extension for Registry.
     * @return self
     */
    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
