<?php

namespace Charcoal\ImageCompression\Provider\Chain;

use Charcoal\ImageCompression\Provider\AbstractProvider;
use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\ProviderInterface;
use Exception;

/**
 * Chain Provider
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
     * {@inheritdoc}
     *
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
                continue;
            } catch (Exception $e) {
                throw new ProviderException(
                    'There was an error processing the chain provider',
                    $e->getCode(),
                    $e
                );
            }
        }

        return false;
    }

    public function compressionCount(): int
    {
        return \array_reduce(
            $this->providers,
            fn($carry, $provider) => ($carry + $provider->compressionCount()),
            0
        );
    }
}
