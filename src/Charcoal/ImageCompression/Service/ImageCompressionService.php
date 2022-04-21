<?php

namespace Charcoal\ImageCompression\Service;

use Charcoal\Admin\Ui\FeedbackContainerTrait;
use Charcoal\ImageCompression\BatchCompressionConfig;
use Charcoal\ImageCompression\Contract\Model\RegistryInterface;
use Charcoal\ImageCompression\Helper\Progression;
use Charcoal\ImageCompression\ImageCompressionConfig;
use Charcoal\ImageCompression\ImageCompressor;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Model\ModelInterface;
use Charcoal\Source\DatabaseSource;
use Charcoal\Source\DatabaseSourceInterface;
use Charcoal\Translator\TranslatorAwareTrait;
use Generator;
use PDO;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;

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
        if ((isset($this->copmressedFilesIds) && in_array($registry['id'], $this->copmressedFilesIds))
            || $registry->isCompressed()
        ) {
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
     * @return Generator|boolean
     */
    public function batchCompress()
    {
        $this->loadCompressedFilesIds();
        $files = $this->gatherFilesToCompress();

        $numFiles = count($files);

        if (!$numFiles) {
            return false;
        }

        $progress = new Progression($numFiles);

        foreach ($files as $file) {
            yield $progress->updateCompressionCount($this->compress($file))
                           ->setCurrentFile($file)
                           ->progress();
        }

        return false;
    }

    /**
     * @return array
     */
    private function gatherFilesToCompress(): array
    {
        $basePath   = $this->getBatchConfig()->getBasePath();
        $extensions = implode(',', $this->getBatchConfig()->getFileExtensions());

        return $this->globRecursive(
            sprintf('%s/*.{%s}', $basePath, $this->caseInsensitiveGlobPattern($extensions)),
            GLOB_BRACE
        );
    }

    /**
     * @return array
     * @throws RuntimeException When the database couldn't be initialized.
     */
    private function loadCompressedFilesIds(): array
    {
        if (isset($this->copmressedFilesIds)) {
            return $this->copmressedFilesIds;
        }

        $registryProto = clone $this->registryProto();

        /** @var DatabaseSource $source */
        $source = $registryProto->source();
        $query  = $source->setProperties(['id'])
                         ->sqlLoad();

        $db = $source->db();
        if (!$db) {
            throw new RuntimeException(
                'Could not instantiate a database connection.'
            );
        }
        $this->logger->debug($query);

        $sth = $db->prepare($query);
        $sth->execute();
        $this->copmressedFilesIds = (array)$sth->fetchAll(PDO::FETCH_COLUMN, 0);

        return $this->copmressedFilesIds;
    }

    /**
     * @return BatchCompressionConfig
     */
    public function getBatchConfig(): BatchCompressionConfig
    {
        return $this->getImageCompressionConfig()->getBatchConfig();
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
     * Returns a case-insensitive pattern useful for the glob function.
     * For example: "jpg" becomes"[jJ][pP][gG]"
     *
     * @param string $pattern The original string to modify.
     * @return string
     */
    private function caseInsensitiveGlobPattern(string $pattern) : string
    {
        $ciPattern = '';
        $length = strlen($pattern);
        for ($i=0; $i<$length; $i++) {
            $char = substr($pattern, $i, 1);
            if (preg_match('/[^A-Za-z]/', $char)) {
                $ciPattern .= $char;
            } else {
                $ciPattern .= sprintf('[%s%s]', strtolower($char), strtoupper($char));
            }
        }
        return $ciPattern;
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
