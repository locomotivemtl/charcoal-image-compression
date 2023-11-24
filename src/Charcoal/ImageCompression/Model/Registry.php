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
    protected ?int $size = null;

    protected ?int $memorySaved = null;

    protected ?int $originalSize = null;

    /**
     * The full path of the file.
     */
    protected ?string $path = null;

    protected ?string $basename = null;

    protected ?string $filename = null;

    protected ?string $extension = null;

    /**
     * Set and parse the registry data from a file.
     *
     * @param  string $path The file path.
     * @throws RuntimeException When the file path is invalid.
     */
    public function fromFile(string $path): self
    {
        if (!\file_exists($path)) {
            throw new RuntimeException(\sprintf(
                'The file path [%s] does not exist in [%s]',
                $path,
                \get_class($this)
            ));
        }

        $this->setData([
            'id'   => \md5_file($path),
            'path' => $path,
            'size' => \filesize($path),
        ])->setData(\pathinfo($path));

        return $this;
    }

    /**
     * @param  ?string $path Optional, the path of the file to check.
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
        $source->setFilters([
            [
                'property' => 'id',
                'value'    => $this['id'],
            ],
        ]);

        $query = $source->sqlLoadCount();

        $db = $source->db();
        if (!$db) {
            throw new RuntimeException(
                'Could not instantiate a database connection.'
            );
        }
        $this->logger->debug($query);

        $sth = $db->prepare($query);
        $sth->execute();
        $res = (int)$sth->fetchColumn(0);

        return (bool)$res;
    }

    /**
     * The file size.
     */
    public function size(): ?int
    {
        return $this->size;
    }

    /**
     * @param ?int $size Size for Registry.
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
     */
    public function originalSize(): ?int
    {
        return $this->originalSize;
    }

    /**
     * @param ?int $originalSize OriginalSize for Registry.
     */
    public function setOriginalSize(?int $originalSize): self
    {
        $this->originalSize = $originalSize;

        return $this;
    }

    /**
     * The amount of memory saved for the file after compression.
     */
    public function memorySaved(): ?int
    {
        return $this->memorySaved;
    }

    /**
     * @param ?int $memorySaved MemorySaved for Registry.
     */
    public function setMemorySaved(?int $memorySaved): self
    {
        $this->memorySaved = $memorySaved;

        return $this;
    }

    public function basename(): ?string
    {
        return $this->basename;
    }

    /**
     * @param ?string $basename Basename for Registry.
     */
    public function setBasename(?string $basename): self
    {
        $this->basename = $basename;

        return $this;
    }

    public function filename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param ?string $filename Filename for Registry.
     */
    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function extension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param ?string $extension Extension for Registry.
     */
    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
