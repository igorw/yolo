<?php

namespace Yolo\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequestParameterListener implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('request')) {
            $request->attributes->set('request', $request);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => ['onKernelRequest'],
        ];
    }
}
