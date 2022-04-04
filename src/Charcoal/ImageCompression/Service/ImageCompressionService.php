<?php

namespace Charcoal\ImageCompression\Service;

use Charcoal\Admin\Ui\FeedbackContainerTrait;
use Charcoal\ImageCompression\ImageCompressionConfig;
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
        $this->setImageCompressionConfig($data['image-compression/config']);
        $this->setTranslator($data['translator']);
    }

    /**
     * @param string $path The file to compress.
     * @return void
     */
    public function compress(string $path)
    {
        // Fetch file data

        // validate compression

        // compress
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
