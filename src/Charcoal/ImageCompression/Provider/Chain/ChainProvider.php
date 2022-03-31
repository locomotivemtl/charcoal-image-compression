<?php

namespace Charcoal\ImageCompression\Provider\Chain;

use Charcoal\ImageCompression\Provider\AbstractProvider;
use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\ProviderInterface;

/**
 * Class ChainProvider
 */
class ChainProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private array $providers;

    /**
     * @param ProviderInterface[] $providers List of providers to chain.
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param string      $source The source file path.
     * @param string|null $target The target file path, if empty, will overwrite the source file.
     * @return boolean
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool
    {
        foreach ($this->providers as $provider) {
            try {
                // Only stop on successful compression.
                if ($provider->compress($source, $target)) {
                    return true;
                }
            } catch (ProviderException $e) {
                // @todo: Log exception and keep looping through providers
            }
        }

        return false;
    }

    /**
     * @return string|null
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): ?string
    {
        return array_reduce($this->providers, fn($carry, $provider) => ($carry + $provider->compressionCount()));
    }
}
