<?php

namespace Charcoal\ImageCompression\Provider;

/**
 * Abstract Provider
 *
 * @todo Add support for Flysystem instead of only local paths.
 * @todo Determine if it makes sense to provide a method to update registries here.
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @param array<string, mixed> $dependencies The class dependencies
     */
    public function setDependencies(array $dependencies): void
    {
    }
}
