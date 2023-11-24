<?php

namespace Charcoal\ImageCompression;

use Charcoal\ImageCompression\Provider\Chain\ChainProvider;
use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\ProviderInterface;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Image Compressor
 */
class ImageCompressor implements
    Provider\ProviderInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ProviderInterface $provider;

    /**
     * @param array<string, mixed> $data Class dependencies.
     */
    public function __construct(array $data)
    {
        $this->setLogger($data['logger']);

        $this->setProviders($data['providers']);
    }

    /**
     * @param ProviderInterface[] $providers List of providers for the compressor.
     */
    public function setProviders(array $providers): self
    {
        if (\count($providers) > 1) {
            $this->provider = new ChainProvider($providers);
        } else {
            $provider = \array_pop($providers);
            if ($provider) {
                $this->setProvider($provider);
            }
        }

        return $this;
    }

    public function setProvider(ProviderInterface $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool
    {
        try {
            return $this->provider->compress($source, $target);
        } catch (Exception $e) {
            throw new ProviderException(
                \sprintf(
                    'There was a problem while compressing images using [%s]',
                    \get_class($this->provider)
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): int
    {
        try {
            return $this->provider->compressionCount();
        } catch (Exception $e) {
            throw new ProviderException(
                \sprintf(
                    'There was a problem retrieving the compression count from [%s]',
                    \get_class($this->provider)
                ),
                $e->getCode(),
                $e
            );
        }
    }
}
