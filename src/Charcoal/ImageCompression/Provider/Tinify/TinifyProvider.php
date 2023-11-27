<?php

namespace Charcoal\ImageCompression\Provider\Tinify;

use Charcoal\ImageCompression\Provider\ProviderException;
use Charcoal\ImageCompression\Provider\AbstractProvider;
use Exception;

/**
 * Tinify Provider
 *
 * Provides compression functionality via the Tinify API.
 */
class TinifyProvider extends AbstractProvider
{
    /**
     * Flag to keep track if the connection as been validated at least once.
     *
     * TRUE if connected, FALSE is unable to connect, NULL if not tested.
     */
    private ?bool $connectionValidated = null;

    /**
     * The API key.
     */
    private string $key;

    /**
     * @param array<string, mixed> $options Provider options.
     */
    public function __construct(array $options)
    {
        if (isset($options['key'])) {
            $this->key = $options['key'];
        }
    }

    /**
     * @throws ProviderException When a provider is failing.
     */
    public function assertValidConnection(): void
    {
        if (!$this->validateConnection()) {
            throw new ProviderException('Could not validate connection with Tinify API');
        }
    }

    public function compress(string $source, ?string $target = null): bool
    {
        $this->assertValidConnection();

        // Overwrite the source file if target is missing.
        $target = ($target ?? $source);

        if (!\file_exists($source)) {
            return false;
        }

        try {
            $tinifySource = \Tinify\fromFile($source);
            $tinifySource->toFile($target);
        } catch (Exception $e) {
            throw new ProviderException(
                \sprintf('There was a problem compressing [%s] with Tinify provider', $source),
                $e->getCode(),
                $e
            );
        }

        return true;
    }

    public function compressionCount(): int
    {
        $this->assertValidConnection();

        return (int)\Tinify\compressionCount();
    }

    /**
     * Should be called before each query to ensure the right key is being used.
     *
     * @throws ProviderException When the connection as failed.
     */
    public function validateConnection(): bool
    {
        if ($this->connectionValidated === null) {
            try {
                // This key reallocation ensures the API is set with
                // the correct key since the API is rather stateless.
                \Tinify\setKey($this->key);
                $this->connectionValidated = \Tinify\validate();
            } catch (\Exception $e) {
                $this->connectionValidated = false;
                throw new ProviderException(
                    'Could not authenticate with Tinify',
                    $e->getCode(),
                    $e
                );
            }
        }

        return $this->connectionValidated;
    }
}
