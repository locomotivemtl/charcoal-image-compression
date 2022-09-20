<?php

namespace Charcoal\ImageCompression;

use Charcoal\App\Event\AbstractListenerSubscriber;
use Charcoal\App\Event\FileWasUploaded;
use Charcoal\ImageCompression\Event\CompressImageListener;
use League\Event\ListenerRegistry;

/**
 * Subscribe listeners for the ImageCompression package
 */
class ListenerSubscriber extends AbstractListenerSubscriber
{

    /**
     * @param ListenerRegistry $acceptor
     * @return void
     */
    public function subscribeListeners(ListenerRegistry $acceptor): void
    {
        $acceptor->subscribeTo(FileWasUploaded::class, $this->createListener(CompressImageListener::class));
    }
}
