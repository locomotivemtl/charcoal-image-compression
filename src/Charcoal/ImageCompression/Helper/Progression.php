<?php

namespace Charcoal\ImageCompression\Helper;

/**
 * Progression
 */
class Progression
{
    /**
     * @var integer
     */
    public int $total;
    /**
     * @var integer
     */
    public int $current = 0;

    /**
     * @var integer
     */
    public int $compressed = 0;

    /**
     * @param integer $total The progression starting point.
     */
    public function __construct(int $total)
    {
        $this->total   = $total;
    }

    /**
     * @return float|integer
     */
    public function percent()
    {
        return ($this->current / $this->total * 100);
    }

    /**
     * @return self
     */
    public function progress(): self
    {
        $this->current++;

        return $this;
    }

    /**
     * @return integer The amount of compressed files.
     */
    public function compressed(): int
    {
        return $this->compressed;
    }

    /**
     * @param boolean $success If compression was successful.
     * @return void
     */
    public function updateCompressionCount(bool $success)
    {
        if ($success) {
            $this->compressed++;
        }
    }
}
