<?php

namespace Yolo\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImplicitControllerListener implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('request')) {
            $request->attributes->set('request', $request);
        }
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $event->setResponse(new Response((string) $result));
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST   => array('onKernelRequest'),
            KernelEvents::VIEW      => array(array('onKernelView', -512)),
        );
    }
}
