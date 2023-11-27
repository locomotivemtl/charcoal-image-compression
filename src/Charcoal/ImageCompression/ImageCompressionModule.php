<?php

namespace Charcoal\ImageCompression;

use Charcoal\App\Module\AbstractModule;
use Charcoal\App\Module\ModuleInterface;

/**
 * Charcoal Module: Image Compression
 */
class ImageCompressionModule extends AbstractModule implements ModuleInterface
{
    public const ADMIN_CONFIG = 'vendor/locomotivemtl/charcoal-image-compression/config/admin.json';
    public const APP_CONFIG   = 'vendor/locomotivemtl/charcoal-image-compression/config/config.json';

    /**
     * Setup the module's dependencies.
     *
     * @return ImageCompressionModule
     */
    public function setup(): ImageCompressionModule
    {
        /** @var \Pimple\Container */
        $container = $this->app()->getContainer();

        $imageCompressionServiceProvider = new ImageCompressionServiceProvider();
        $container->register($imageCompressionServiceProvider);

        return $this;
    }
}
