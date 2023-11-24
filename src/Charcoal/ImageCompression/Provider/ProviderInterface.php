<?php

namespace Charcoal\ImageCompression\Provider;

/**
 * Compression Provider
 */
interface ProviderInterface
{
    /**
     * @param  string  $source The source file path.
     * @param  ?string $target The target file path, if empty, will overwrite the source file.
     * @return bool
     */
    public function compress(string $source, ?string $target = null): bool;

    public function compressionCount(): int;
}
