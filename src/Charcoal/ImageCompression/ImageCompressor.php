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

    /**
     * @var ProviderInterface
     */
    private ProviderInterface $provider;

    /**
     * @param array $data Initial data.
     */
    public function __construct(array $data)
    {
        $this->setLogger($data['logger']);

        $this->setProviders($data['providers']);
    }

    /**
     * @param array|ProviderInterface[] $providers List of providers for the compressor.
     * @return self
     */
    public function setProviders(array $providers): self
    {
        if (count($providers) > 1) {
            $this->provider = new ChainProvider($providers);
        } else {
            $provider = array_shift($providers);

            if ($provider) {
                $this->setProvider($provider);
            }
        }

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
        // There is no provider in the config
        if (!isset($this->provider)) {
            $this->logger->warning('There are no compression provider(s) in the config.'.
                ' {@see https://github.com/locomotivemtl/charcoal-image-compression'.
                ' for more details on implementation.}');

            return false;
        }

        try {
            return $this->provider->compress($source, $target);
        } catch (Exception $e) {
            throw new ProviderException(
                sprintf('There was a problem while compressing images using [%s] class', get_class($this->provider)),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return string|null
     * @throws ProviderException When a provider is failing.
     */
    public function compressionCount(): ?string
    {
        if (!isset($this->provider)) {
            return false;
        }

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
