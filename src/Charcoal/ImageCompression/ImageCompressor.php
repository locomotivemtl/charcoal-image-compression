<?php

namespace Charcoal\ImageCompression;

use Charcoal\ImageCompression\Provider\Chain\ChainProvider;
use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\ProviderInterface;
use Exception;

/**
 * Image Compressor
 */
class ImageCompressor implements Provider\ProviderInterface
{
    /**
     * @var ProviderInterface
     */
    private ProviderInterface $provider;

    /**
     * @param ProviderInterface|ProviderInterface[] $providers The provider(s) for the compressor.
     */
    public function __construct($providers)
    {
        if (is_array($providers)) {
            $this->setProviders($providers);
        } else {
            $this->setProvider($providers);
        }
    }

    /**
     * @param array|ProviderInterface[] $providers List of providers for the compressor.
     * @return self
     */
    public function setProviders(array $providers): self
    {
        $this->provider = new ChainProvider($providers);

        return $this;
    }

    /**
     * @param ProviderInterface $provider Provider for the compressor.
     * @return $this
     */
    public function setProvider(ProviderInterface $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param string      $source The source file path.
     * @param string|null $target The target file path, if empty, will overwrite the source file.
     * @return boolean
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool
    {
        try {
            $this->provider->compress($source, $target);
        } catch (Exception $e) {
            throw new ProviderException(
                sprintf('There was a problem while compressing images using [%s] class', get_class($this->provider)),
                $e->getCode(),
                $e
            );
        }

        return true;
    }

    /**
     * @return string|null
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): ?string
    {
        try {
            return $this->provider->compressionCount();
        } catch (Exception $e) {
            throw new ProviderException(
                sprintf(
                    'There was a problem getting the compression count from the [%s] class',
                    get_class($this->provider)
                ),
                $e->getCode(),
                $e
            );
        }
    }
}
