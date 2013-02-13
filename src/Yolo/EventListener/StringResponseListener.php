<?php

namespace Yolo\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StringResponseListener implements EventSubscriberInterface
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $event->setResponse(new Response((string) $result));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW      => [['onKernelView', -512]],
        ];
    }
}
