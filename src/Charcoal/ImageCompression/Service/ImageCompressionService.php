<?php

namespace Charcoal\ImageCompression\Service;

use Charcoal\Admin\Ui\FeedbackContainerTrait;
use Charcoal\ImageCompression\Contract\Model\RegistryInterface;
use Charcoal\ImageCompression\ImageCompressionConfig;
use Charcoal\ImageCompression\ImageCompressor;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Model\ModelInterface;
use Charcoal\Source\DatabaseSourceInterface;
use Charcoal\Translator\TranslatorAwareTrait;
use Psr\Log\LoggerAwareTrait;

/**
 * Image Compression Service
 */
class ImageCompressionService
{
    use TranslatorAwareTrait;
    use FeedbackContainerTrait;
    use LoggerAwareTrait;
    use ModelFactoryTrait;

    /**
     * @var ImageCompressor
     */
    private ImageCompressor $compressor;

    /**
     * @var ImageCompressionConfig
     */
    private ImageCompressionConfig $imageCompressionConfig;

    /**
     * @param array $data The initial data array.
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->setCompressor($data['compressor']);
        $this->setImageCompressionConfig($data['image-compression/config']);
        $this->setTranslator($data['translator']);
        $this->setModelFactory($data['factory']);
        $this->setLogger($data['logger']);
    }

    /**
     * @param string $path The file to compress.
     * @return boolean
     */
    public function compress(string $path): bool
    {
        // Fetch file data
        /** @var RegistryInterface $file */
        $registry = clone $this->registryProto();
        try {
            $registry->fromFile($path);
        } catch (\Exception $e) {
            // Don't compress if registry data can't be loaded.
            // This usually means that an image is no longer on the server.
            $this->logger->warning($e->getMessage());

            return false;
        }

        // validate compression
        if ($registry->isCompressed()) {
            // Don't compress more than once.
            return false;
        }

        // compress
        if ($this->getCompressor()->compress($path)) {
            $registry->setOriginalSize($registry['size']);

            clearstatcache();
            $registry->fromFile($registry['path']);

            // After compressing, it's possible a file that was once compressed,
            // removed and re-uploaded from the original.
            if ($registry->isCompressed()) {
                // Don't compress more than once.
                return $registry->update();
            }

            return $registry->save();
        }

        return false;
    }

    /**
     * @return void
     */
    public function compressAll()
    {

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

    /**
     * @return ImageCompressor
     */
    public function getCompressor(): ImageCompressor
    {
        return $this->compressor;
    }

    /**
     * @param ImageCompressor $compressor Compressor for ImageCompressionService.
     * @return self
     */
    public function setCompressor(ImageCompressor $compressor): self
    {
        $this->compressor = $compressor;

        return $this;
    }

    /**
     * @return RegistryInterface|mixed
     */
    protected function registryProto()
    {
        if (isset($this->registryProto)) {
            return $this->registryProto;
        }

        $this->registryProto = $this->modelFactory()->create($this->getImageCompressionConfig()->getRegistryObject());
        $this->createObjTable($this->registryProto);

        return $this->registryProto;
    }

    // Utils
    // ==========================================================================

    /**
     * @param ModelInterface $proto Prototype to ensure table creation for.
     * @return void
     */
    private function createObjTable(ModelInterface $proto)
    {
        /** @var DatabaseSourceInterface $source */
        $source = $proto->source();

        if ($source->tableExists() === false) {
            $source->createTable();
            $msg = $this->translator()->translate('Database table created for "{{ objType }}".', [
                '{{ objType }}' => $proto->objType()
            ]);
            $this->addFeedback(
                'notice',
                '<span class="fa fa-asterisk" aria-hidden="true"></span><span>&nbsp; '.$msg.'</span>'
            );
        }
    }

    /**
     * Recursively find path names matching a pattern
     *                         an empty array if no file matched or FALSE on error.
     * @param string  $pattern The search pattern.
     * @param integer $flags   The glob flags.
     * @return array   Returns an array containing the matched files/directories,
     * @see glob() for a description of the function and its parameters.
     *
     */
    private function globRecursive(string $pattern, int $flags = 0): array
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern).'/*', (GLOB_ONLYDIR | GLOB_NOSORT)) as $dir) {
            $files = array_merge($files, $this->globRecursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }
}
