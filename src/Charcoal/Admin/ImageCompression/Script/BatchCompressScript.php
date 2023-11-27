<?php

namespace Charcoal\Admin\ImageCompression\Script;

use Charcoal\App\Script\AbstractScript;
use Charcoal\ImageCompression\Service\ImageCompressionService;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * CLI Script: Batch Compression
 */
class BatchCompressScript extends AbstractScript
{
    private ImageCompressionService $imageCompressionService;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function defaultArguments(): array
    {
        $config = $this->imageCompressionService->getBatchConfig();

        $arguments = [
            'path' => [
                'longPrefix'   => 'path',
                'description'  => 'Directory (relative) to process.',
                'defaultValue' => $config->getBasePath(),
            ]
        ];

        return \array_merge(parent::defaultArguments(), $arguments);
    }

    /**
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        unset($request);

        $climate = $this->climate();
        $climate->arguments->parse();

        $path = $climate->arguments->get('path');

        $climate->underline()->out(
            \sprintf('Optimize images ("%s")', $path)
        );

        $progressBar = $climate->progress()->total(100);
        $compressedFiles = 0;

        foreach ($this->imageCompressionService->batchCompress($path) as $progress) {
            $progressBar->current(
                $progress->percent(),
                \sprintf(
                    '[%s / %s] Compressing: %s',
                    $progress->current,
                    $progress->total,
                    $progress->getCurrentFile()
                )
            );
            $compressedFiles = $progress->compressed();
        };

        $climate->underline(
            \sprintf('%s files were compressed', $compressedFiles)
        );

        return $response;
    }

    /**
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->imageCompressionService = $container['image-compression'];
    }
}
