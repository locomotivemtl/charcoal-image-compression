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
     * @return integer|null
     */
    public function size(): ?int;

    /**
     * The size of the file before compression.
     *
     * @return integer|null
     */
    public function originalSize(): ?int;

    /**
     * The amount of memory saved for the file after compression.
     *
     * @return integer|null
     */
    public function memorySaved(): ?int;

    /**
     * @return string|null
     */
    public function basename(): ?string;

    /**
     * @return string|null
     */
    public function filename(): ?string;

    /**
     * @return string|null
     */
    public function extension(): ?string;
}
