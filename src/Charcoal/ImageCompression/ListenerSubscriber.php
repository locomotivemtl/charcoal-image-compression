<?php

namespace Charcoal\ImageCompression;

use Charcoal\Event\AbstractListenerSubscriber;
use Charcoal\Event\Events\FileWasUploaded;
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
