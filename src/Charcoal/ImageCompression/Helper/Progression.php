<?php

namespace Charcoal\ImageCompression\Helper;

class Progression
{
    public int $total;

    public int $current = 0;

    public int $compressed = 0;

    private string $currentFile;

    /**
     * @param int $total The progression starting point.
     */
    public function __construct(int $total)
    {
        $this->total = $total;
    }

    /**
     * @return float|int
     */
    public function percent()
    {
        return ($this->current / $this->total * 100);
    }

    public function progress(): self
    {
        $this->current++;

        return $this;
    }

    /**
     * Returns the number of compressed files.
     */
    public function compressed(): int
    {
        return $this->compressed;
    }

    /**
     * Indicates if compression was successful.
     */
    public function updateCompressionCount(bool $success): self
    {
        if ($success) {
            $this->compressed++;
        }

        return $this;
    }

    public function getCurrentFile(): string
    {
        return $this->currentFile;
    }

    /**
     * Indicates the file currently being compressed.
     */
    public function setCurrentFile(string $currentFile): self
    {
        $this->currentFile = $currentFile;

        return $this;
    }
}
