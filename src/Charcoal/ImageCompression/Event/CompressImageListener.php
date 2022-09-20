<?php

namespace Charcoal\ImageCompression\Event;

use Charcoal\Event\AbstractEventListener;
use Charcoal\ImageCompression\Service\ImageCompressionService;
use Pimple\Container;

/**
 * Class CompressImageListener
 */
class CompressImageListener extends AbstractEventListener
{
    private ImageCompressionService $compressionService;

    /**
     * @param Container $container Pimple DI Container.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->compressionService = $container['image-compression'];
    }

    /**
     * @param object $event The event object being fired.
     * @return void
     */
    public function __invoke(object $event)
    {
        $this->logger->notice(sprintf('Listener [%s] triggered by [%s]', get_class($this), get_class($event)));
        // Exit early if feature disabled
        $config = $this->compressionService->getImageCompressionConfig();
        if (!$config->getAutoCompress()) {
            return;
        }

        $file = $event->getFile();

        if (!$this->isValidImageSrc($file)) {
            return;
        }

        $size = filesize($file);

        if ($this->compressionService->compress($file)) {
            clearstatcache(true, $file);
            $newSize    = filesize($file);
            $spaceFreed = ($size - $newSize);
            $this->logger->notice(
                'Image file compressed',
                [
                    'path'         => $file,
                    'size'         => $this->formatBytes($size),
                    'newSize'      => $this->formatBytes($newSize),
                    'spaceFreed'   => $this->formatBytes($spaceFreed),
                    'percentFreed' => round(($spaceFreed * 100 / $size), 2) . '%',
                ]
            );
        } else {
            $this->logger->warning('Could not compress file', ['file' => $file]);
        }
    }

    /**
     * @param $bytes
     * @param $decimals
     * @return string
     */
    private function formatBytes($bytes, $decimals = 2): string
    {
        $sz     = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", ($bytes / pow(1024, $factor))) . $sz[$factor];
    }

    /**
     * Validate that the file is an image and is of the good
     *
     * @param string $src The file src.
     * @return bool
     */
    public function isValidImageSrc(string $src): bool
    {
        if (!$src || !file_exists($src)) {
            return false;
        }

        $mime = mime_content_type($src);
        if (substr($mime, 0, 5) !== 'image') {
            return false;
        }

        if (extension_loaded('exif') && function_exists('exif_imagetype')) {
            $imageType = exif_imagetype($src);
            if ($imageType === false) {
                return false;
            }
        } else {
            $srcImgInfo = getimagesize($src);
            if ($srcImgInfo === false) {
                return false;
            }
            $imageType = $srcImgInfo[2];
        }

        // check target image type
        $imgTypes = [
            IMAGETYPE_GIF  => IMG_GIF,
            IMAGETYPE_JPEG => IMG_JPEG,
            IMAGETYPE_PNG  => IMG_PNG,
            IMAGETYPE_BMP  => IMG_WBMP,
            IMAGETYPE_WBMP => IMG_WBMP
        ];
        if (!isset($imgTypes[$imageType])) {
            return false;
        }

        return true;
    }
}
