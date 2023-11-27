<?php

namespace Charcoal\ImageCompression;

use Charcoal\Factory\FactoryInterface;
use Charcoal\Factory\GenericFactory;
use Charcoal\ImageCompression\Provider\ProviderInterface;
use Charcoal\ImageCompression\Provider\Tinify\TinifyProvider;
use Charcoal\ImageCompression\Service\ImageCompressionService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Service Provider: Image Compression
 */
class ImageCompressionServiceProvider implements ServiceProviderInterface
{
    /**
     * @return void
     */
    public function register(Container $container)
    {
        $container['image-compression/config'] = function (Container $container) {
            $compressionConfig = new ImageCompressionConfig();

            $moduleSettings = $container['config']->get('modules.charcoal/image-compression/image-compression');
            if ($moduleSettings) {
                $compressionConfig->merge($moduleSettings);
            }

            $configSettings = $container['config']->get('image_compression');
            if ($configSettings) {
                $compressionConfig->merge($configSettings);
            }

            return $compressionConfig;
        };

        /**
         * Factorize all providers in an array.
         *
         * @return array<ProviderInterface>
         */
        $container['image-compression/providers'] = function (Container $container) {
            $providers = $container['image-compression/config']->get('providers');
            if (!$providers) {
                return [];
            }

            return \array_map(function ($provider) use ($container) {
                $type = ($provider['type'] ?? null);
                if (!$type) {
                    return null;
                }

                /** @var FactoryInterface $factory */
                $factory = $container['image-compression/provider/factory'];

                /** @var ProviderInterface $providerObject */
                return $factory->create($type, $provider);
            }, $providers);
        };

        /**
         * @return \Charcoal\Factory\FactoryInterface
         */
        $container['image-compression/provider/factory'] = function (Container $container) {
            return new GenericFactory([
                'base_class'       => ProviderInterface::class,
                'resolver_options' => [
                    'suffix' => 'Provider'
                ],
                'map'              => [
                    'tinify' => TinifyProvider::class,
                ],
                'callback'         => function ($provider) use ($container) {
                    $provider->setDependencies([
                        'logger' => $container['logger'],
                    ]);
                },
            ]);
        };

        $container['image-compressor'] = function (Container $container) {
            return new ImageCompressor([
                'providers' => $container['image-compression/providers'],
                'logger'    => $container['logger'],
            ]);
        };

        $container['image-compression'] = function (Container $container) {
            return new ImageCompressionService([
                'compressor'               => $container['image-compressor'],
                'image-compression/config' => $container['image-compression/config'],
                'translator'               => $container['translator'],
                'factory'                  => $container['model/factory'],
                'logger'                   => $container['logger'],
            ]);
        };
    }
}
