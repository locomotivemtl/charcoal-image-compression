<?php

namespace Charcoal\ImageCompression;

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

        $container['image-compression/providers'] = function (Container $container) {
            $providers = $container['image-compression/config']['providers'];
        };

        /**
         * @param Container $container
         * @return ImageCompressionService
         */
        $container['image-compression'] = function (container $container) {
            return new ImageCompressionService([
                'image-compression/config' => $container['image-compression/config'],
            ]);
        };
    }
}
