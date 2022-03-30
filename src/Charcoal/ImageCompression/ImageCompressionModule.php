<?php

namespace Charcoal\ImageCompression;

use Charcoal\App\Module\AbstractModule;
use Charcoal\App\Module\ModuleInterface;

/**
 * Charcoal Module: Image Compression
 */
class ImageCompressionModule extends AbstractModule implements ModuleInterface
{

    /**
     * Setup the module's dependencies.
     *
     * @return ImageCompressionModule
     */
    public function setup(): ImageCompressionModule
    {
        $container = $this->app()->getContainer();

        $imageCompressionServiceProvider = new ImageCompressionServiceProvider();
        $container->register($imageCompressionServiceProvider);

        return $this;
    }
}
