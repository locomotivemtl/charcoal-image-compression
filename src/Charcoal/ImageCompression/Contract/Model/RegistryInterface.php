<?php

namespace Charcoal\ImageCompression\Contract\Model;

/**
 * Interface: RegistryInterface
 * @package Charcoal\ImageCompression\Contract\Model
 */
interface RegistryInterface
{
    /**
     * The file size.
     *
     * @return integer
     */
    public function size(): int;

    /**
     * The size of the file before compression.
     *
     * @return integer
     */
    public function originalSize(): int;

    /**
     * The amount of memory saved for the file after compression.
     *
     * @return integer
     */
    public function memorySaved(): int;

    /**
     * @return string
     */
    public function basename(): string;

    /**
     * @return string
     */
    public function filename(): string;

    /**
     * @return string
     */
    public function extension(): string;
}
