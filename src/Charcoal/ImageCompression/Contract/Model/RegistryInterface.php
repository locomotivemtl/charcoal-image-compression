<?php

namespace Charcoal\ImageCompression\Contract\Model;

/**
 * Registry Interface
 */
interface RegistryInterface
{
    /**
     * The file size.
     */
    public function size(): ?int;

    /**
     * The size of the file before compression.
     */
    public function originalSize(): ?int;

    /**
     * The amount of memory saved for the file after compression.
     */
    public function memorySaved(): ?int;

    public function basename(): ?string;

    public function filename(): ?string;

    public function extension(): ?string;
}
