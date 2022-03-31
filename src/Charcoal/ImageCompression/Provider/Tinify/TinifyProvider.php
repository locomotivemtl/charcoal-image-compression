<?php

namespace Charcoal\ImageCompression\Provider\Tinify;

use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\AbstractProvider;
use Exception;

/**
 * Tinify Provider
 *
 * Provides compression functionalities via Tinify apis.
 */
class TinifyProvider extends AbstractProvider
{
    /**
     * Flag to keep track if the connection as been validated at least once.
     *
     * @var boolean $connectionValidated
     */
    private bool $connectionValidated = false;

    /**
     * @var string $key
     */
    private string $key;

    /**
     * @param array $options Provider options.
     */
    public function __construct(array $options)
    {
        if (isset($options['key'])) {
            $this->key = $options['key'];
        }
    }

    /**
     * @param string      $source The source file path.
     * @param string|null $target The target file path, if empty, will overwrite the source file.
     * @return boolean
     * @throws ProviderException When a provider is failing.
     */
    public function compress(string $source, ?string $target = null): bool
    {
        if (!$this->validateConnection()) {
            throw new ProviderException('Could not validate connection with Tinify Api.');
        }

        // Overwrite the source file if target is missing.
        $target = ($target ?? $source);

        if (!file_exists($source)) {
            return false;
        }

        try {
            $tinifySource = \Tinify\fromFile($source);
            $tinifySource->toFile($target);
        } catch (Exception $e) {
            throw new ProviderException(
                sprintf('There was a problem compressing [%s] with Tinify provider', $source),
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
        if (!$this->validateConnection()) {
            throw new ProviderException('Could not validate connection with Tinify Api.');
        }

        return \Tinify\compressionCount();
    }

    /**
     * ValidateConnection should be called before each query to ensure the right key is being used.
     *
     * @return boolean
     * @throws ProviderException When the connection as failed.
     */
    public function validateConnection(): bool
    {
        if (!$this->connectionValidated) {
            try {
                // This key reallocation ensures the API is set with the correct key since the API is rather stateless.
                \tinify\setKey($this->key);
                $this->connectionValidated = \Tinify\validate();
            } catch (\Exception $e) {
                throw new ProviderException('Could not authenticate with Tinify', $e->getCode(), $e);
            }
        }

        return $this->connectionValidated;
    }
}
