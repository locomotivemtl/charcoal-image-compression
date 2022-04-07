<?php

namespace Charcoal\Admin\ImageCompression\Script;

use Charcoal\App\Script\AbstractScript;
use Charcoal\ImageCompression\Service\ImageCompressionService;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BatchCompress
 */
class BatchCompressScript extends AbstractScript
{
    /**
     * @var ImageCompressionService
     */
    private ImageCompressionService $imageCompressionService;

    /**
     * @var string
     */
    private string $path;

    /**
     * @return array
     */
    public function defaultArguments(): array
    {
        $config = $this->imageCompressionService->getBatchConfig();

        $arguments = [
            'path' => [
                'longPrefix'   => 'path',
                'description'  => 'Directory (relative) to process.',
                'defaultValue' => $config->getBasePath()
            ]
        ];

        return array_merge(parent::defaultArguments(), $arguments);
    }

    /**
     * Run the script.
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        unset($request);

        $climate = $this->climate();
        $climate->arguments->parse();

        $path = $climate->arguments->get('path');

        $climate->underline()->out(
            sprintf('Optimize images ("%s")', $path)
        );

        $progressBar = $climate->progress()->total(100);
        $compressedFiles = 0;

        foreach ($this->imageCompressionService->batchCompress() as $progress) {
            $progressBar->current(
                $progress->percent(),
                sprintf('[%s / %s] Compressing: %s', $progress->current, $progress->total, $progress->getCurrentFile())
            );
            $compressedFiles = $progress->compressed();
        };

        $climate->underline(
            sprintf('%s files were compressed', $compressedFiles)
        );

        return $response;
    }

    /**
     * Give an opportunity to children classes to inject dependencies from a Pimple Container.
     *
     * Does nothing by default, reimplement in children classes.
     *
     * The `$container` DI-container (from `Pimple`) should not be saved or passed around, only to be used to
     * inject dependencies (typically via setters).
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->imageCompressionService = $container['image-compression'];
    }
}
