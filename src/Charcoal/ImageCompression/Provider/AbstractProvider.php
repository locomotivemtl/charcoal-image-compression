<?php

namespace Charcoal\ImageCompression\Provider;

/**
 * Abstract Provider
 */
abstract class AbstractProvider implements ProviderInterface
{

    // @todo: add support for Flysystem instead of only local paths.

    // @todo: Determine if it makes sense to provide a method to update registries here.

    /**
     * @param array $dependencies The dependencies array.
     * @return void
     */
    public function setDependencies(array $dependencies)
    {
    }
}
