<?php

namespace functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Yolo\Application;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function beforeEventShouldFireBeforeRoute()
    {
        $app = new Application();

        $app->before(function (GetResponseEvent $event) {
            $request = $event->getRequest();
            $event->setResponse(new Response('before'));
        });

        $app->get('/', function (Request $request) {
            return new Response('foo');
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame('before', $response->getContent());
    }

    /** @test */
    public function afterEventShouldAppendToRoute()
    {
        $app = new Application();

        $app->get('/', function (Request $request) {
            return new Response('foo');
        });

        $app->after(function (FilterResponseEvent $event) {
            $response = $event->getResponse();
            $response->setContent($response->getContent().'.after');
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame('foo.after', $response->getContent());
    }

    /** @test */
    public function beforeAndAfterEventsShouldBothFireWithEarlyEvent()
    {
        $app = new Application();

        $app->before(function (GetResponseEvent $event) {
            $request = $event->getRequest();
            $event->setResponse(new Response('before'));
        }, Application::EARLY_EVENT);

        $app->after(function (FilterResponseEvent $event) {
            $response = $event->getResponse();
            $response->setContent($response->getContent().'.after');
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame('before.after', $response->getContent());
    }

    /**
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function normalPriorityBeforeEventWithoutRouteShouldNotFire()
    {
        $app = new Application();

        $app->before(function (GetResponseEvent $event) {
            $request = $event->getRequest();
            $event->setResponse(new Response('before'));
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
    }
}
