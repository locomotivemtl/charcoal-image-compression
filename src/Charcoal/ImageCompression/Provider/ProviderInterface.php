<?php

namespace Charcoal\ImageCompression\Provider;

/**
 * Interface: ProviderInterface
 * @package Charcoal\ImageCompression\Provider
 */
interface ProviderInterface
{
    /**
     * @param string      $source The source file path.
     * @param string|null $target The target file path, if empty, will overwrite the source file.
     * @return boolean
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool;

    /**
     * @return string|null
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): ?string;
}
