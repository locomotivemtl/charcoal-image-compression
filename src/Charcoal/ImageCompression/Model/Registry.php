<?php

namespace Charcoal\ImageCompression\Model;

use Charcoal\ImageCompression\Contract\Model\RegistryInterface;
use Charcoal\Model\AbstractModel;
use Charcoal\Source\DatabaseSource;
use RuntimeException;

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
    protected ?int $size = null;

    /**
     * @var integer|null
     */
    protected ?int $memorySaved = null;

    /**
     * @var integer|null
     */
    protected ?int $originalSize = null;

    /**
     * The full path of the file.
     *
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * @var string|null
     */
    protected ?string $basename = null;

    /**
     * @var string|null
     */
    protected ?string $filename = null;

    /**
     * @var string|null
     */
    protected ?string $extension = null;

    /**
     * Set and parse the registry data from a file.
     *
     * @param string $path The file path.
     * @return self
     * @throws RuntimeException When the file path is invalid.
     */
    public function fromFile(string $path): RegistryInterface
    {
        if (!file_exists($path)) {
            throw new RuntimeException(sprintf(
                'The file path [%s] doesn\'t exist in [%s]',
                $path,
                get_class($this)
            ));
        }

        $this->setData([
            'id'   => md5_file($path),
            'path' => $path,
            'size' => filesize($path),
        ])->setData(pathinfo($path));

        return $this;
    }

    /**
     * @param string|null $path Optional, the path of the file to check.
     * @return boolean
     * @throws RuntimeException When the database connection fails.
     */
    public function isCompressed(string $path = null): bool
    {
        if ($path) {
            $this->fromFile($path);
        }

        // Maybe we should throw an error here.
        if (!$this['id']) {
            return false;
        }

        /** @var DatabaseSource $source */
        $source = $this->source();
        $query  = $source->setFilters([[
            'property' => 'id',
            'value'    => $this['id']
        ]])->sqlLoadCount();

        $db = $this->source()->db();
        if (!$db) {
            throw new RuntimeException(
                'Could not instantiate a database connection.'
            );
        }
        $this->logger->debug($query);

        $sth = $db->prepare($query);
        $sth->execute();
        $res = $sth->fetchColumn(0);

        return (bool)(int)$res;
    }

    /**
     * The file size.
     *
     * @return integer|null
     */
    public function size(): ?int
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

        if ($this->originalSize()) {
            $this->setMemorySaved($this->originalSize() - $this->size);
        }

        return $this;
    }

    /**
     * The size of the file before compression.
     *
     * @return integer|null
     */
    public function originalSize(): ?int
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
     * @return integer|null
     */
    public function memorySaved(): ?int
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
     * @return string|null
     */
    public function basename(): ?string
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
     * @return string|null
     */
    public function filename(): ?string
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
     * @return string|null
     */
    public function extension(): ?string
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
