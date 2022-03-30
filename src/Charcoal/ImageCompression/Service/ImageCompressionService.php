<?php

namespace Charcoal\ImageCompression\Service;

use Charcoal\ImageCompression\ImageCompressionConfig;

/**
 * Image Compression Service
 */
class ImageCompressionService
{
    private ImageCompressionConfig $imageCompressionConfig;

    /**
     * @param array $data The initial data array.
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->setImageCompressionConfig($data['image-compression/config']);
    }

    /**
     * @return ImageCompressionConfig
     */
    public function getImageCompressionConfig(): ImageCompressionConfig
    {
        return $this->imageCompressionConfig;
    }

    /**
     * @param ImageCompressionConfig $imageCompressionConfig ImageCompressionConfig for ImageCompressionService.
     * @return self
     */
    public function setImageCompressionConfig(ImageCompressionConfig $imageCompressionConfig): ImageCompressionService
    {
        $this->imageCompressionConfig = $imageCompressionConfig;

        return $this;
    }

}
