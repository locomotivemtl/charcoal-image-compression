<?php

namespace Charcoal\ImageCompression\Service;

// use Charcoal\Admin\Ui\FeedbackContainerTrait;
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
    // use FeedbackContainerTrait;
    use LoggerAwareTrait;
    use ModelFactoryTrait;

    private ImageCompressor $compressor;

    private ImageCompressionConfig $imageCompressionConfig;

    /**
     * List of compressed file IDs from the Registry.
     */
    private ?array $copmressedFilesIds = null;

    /**
     * The singleton Registry model.
     *
     * @var (RegistryInterface&ModelInterface)
     */
    private ?RegistryInterface $registryProto = null;

    /**
     * @param array<string, mixed> $data Class dependencies.
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
     */
    public function compress(string $path): bool
    {
        // Fetch file data
        /** @var \Charcoal\ImageCompression\Model\Registry */
        $registry = clone $this->registryProto();
        try {
            $registry->fromFile($path);
        } catch (\Exception $e) {
            // Don't compress if registry data can't be loaded.
            // This usually means that an image is no longer on the server.
            $this->logger->warning($e->getMessage());

            return false;
        }

        // Validate compression
        if (
            (
                \is_array($this->copmressedFilesIds) &&
                \in_array($registry['id'], $this->copmressedFilesIds)
            ) ||
            $registry->isCompressed()
        ) {
            // Don't compress more than once.
            return false;
        }

        // compress
        if ($this->getCompressor()->compress($path)) {
            $registry->setOriginalSize($registry['size']);

            \clearstatcache();

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
     * @return \Generator<bool>
     */
    public function batchCompress()
    {
        $this->loadCompressedFilesIds();
        $files = $this->gatherFilesToCompress();

        $numFiles = \count($files);

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
     * @return string[]
     */
    private function gatherFilesToCompress(): array
    {
        $basePath   = $this->getBatchConfig()->getBasePath();
        $extensions = \implode(',', $this->getBatchConfig()->getFileExtensions());

        return $this->globRecursive(
            \sprintf('%s/*.{%s}', $basePath, $extensions),
            \GLOB_BRACE
        );
    }

    /**
     * @return (int|string)[]
     * @throws RuntimeException When the database couldn't be initialized.
     */
    private function loadCompressedFilesIds(): array
    {
        if (\is_array($this->copmressedFilesIds)) {
            return $this->copmressedFilesIds;
        }

        $registry = clone $this->registryProto();

        /** @var DatabaseSource $source */
        $source = $registry->source();
        $source->setProperties([ 'id' ]);
        $query  = $source->sqlLoad();

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

    public function getBatchConfig(): BatchCompressionConfig
    {
        return $this->getImageCompressionConfig()->getBatchConfig();
    }

    public function getImageCompressionConfig(): ImageCompressionConfig
    {
        return $this->imageCompressionConfig;
    }

    public function setImageCompressionConfig(ImageCompressionConfig $imageCompressionConfig): self
    {
        $this->imageCompressionConfig = $imageCompressionConfig;

        return $this;
    }

    public function getCompressor(): ImageCompressor
    {
        return $this->compressor;
    }

    public function setCompressor(ImageCompressor $compressor): self
    {
        $this->compressor = $compressor;

        return $this;
    }

    /**
     * @return (RegistryInterface&ModelInterface)
     */
    protected function registryProto(): RegistryInterface
    {
        if ($this->registryProto) {
            return $this->registryProto;
        }

        $this->registryProto = $this->modelFactory()->create($this->getImageCompressionConfig()->getRegistryObject());
        $this->createObjTable($this->registryProto);

        return $this->registryProto;
    }

    // Utils
    // ==========================================================================

    /**
     * @param (RegistryInterface&\Charcoal\Model\AbstractModel) $proto
     */
    private function createObjTable(ModelInterface $proto): void
    {
        /** @var DatabaseSourceInterface $source */
        $source = $proto->source();

        if ($source->tableExists() === false) {
            $source->createTable();
            // $msg = $this->translator()->translate('Database table created for "{{ objType }}".', [
            //     '{{ objType }}' => $proto->objType()
            // ]);
            // $this->addFeedback(
            //     'notice',
            //     '<span class="fa fa-asterisk" aria-hidden="true"></span><span>&nbsp; '.$msg.'</span>'
            // );
        }
    }

    /**
     * Recursively find path names matching a pattern an empty array
     * if no file matched or FALSE on error.
     *
     * @param  string $pattern The search pattern.
     * @param  int    $flags   The glob flags.
     * @return string[] Returns an array containing the matched files/directories,
     *     See {@see glob()} for a description of the function and its parameters.
     */
    private function globRecursive(string $pattern, int $flags = 0): array
    {
        $files = \glob($pattern, $flags);

        foreach (\glob(\dirname($pattern) . '/*', (\GLOB_ONLYDIR | \GLOB_NOSORT)) as $dir) {
            $files = \array_merge(
                $files,
                $this->globRecursive($dir . '/' . \basename($pattern), $flags)
            );
        }

        return $files;
    }
}
