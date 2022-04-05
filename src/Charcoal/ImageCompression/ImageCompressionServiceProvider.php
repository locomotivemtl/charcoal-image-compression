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
     * @param Container $container Pimple DI container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * @return ImageCompressionConfig
         */
        $container['image-compression/config'] = function (container $container) {
            $configData = $container['config']->get('modules.charcoal/image-compression/image-compression');

            return new ImageCompressionConfig($configData);
        };

        /**
         * Factorize all providers in an array.
         *
         * @param Container $container
         * @return array
         */
        $container['image-compression/providers'] = function (Container $container) {
            $providers = $container['image-compression/config']->get('providers');

            if (!$providers) {
                return [];
            }

            return array_map(function ($provider) use ($container) {
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
         * @param Container $container The Pimple DI container.
         * @return FactoryInterface
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
                        'logger' => $container['logger']
                    ]);
                }
            ]);
        };

        /**
         * @param Container $container The Pimple DI container.
         * @return ImageCompressor
         */
        $container['image-compressor'] = function (Container $container) {
            return new ImageCompressor([
                'providers' => $container['image-compression/providers'],
                'logger'    => $container['logger'],
            ]);
        };

        /**
         * @param Container $container
         * @return ImageCompressionService
         */
        $container['image-compression'] = function (container $container) {
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
