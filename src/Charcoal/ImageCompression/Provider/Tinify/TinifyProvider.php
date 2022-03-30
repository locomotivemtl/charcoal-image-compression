<?php

namespace Charcoal\ImageCompression\Provider\Tinify;

use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\AbstractProvider;

/**
 * Tinify Provider
 *
 * Provides compression functionalities via Tinify apis.
 */
class TinifyProvider extends AbstractProvider
{

    /**
     * @param string      $source The source file path.
     * @param string|null $target The target file path, if empty, will overwrite the source file.
     * @return boolean
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool
    {
        return true;
    }

    /**
     * @return string|null
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): ?string
    {
        return '';
    }
}
