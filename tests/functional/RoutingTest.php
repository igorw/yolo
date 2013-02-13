<?php

namespace functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Yolo\Application;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $app = new Application();

        $app->get('/', function (Request $request) {
            return new Response("Hallo welt, got swag yo!\n");
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertSame("Hallo welt, got swag yo!\n", $response->getContent());
    }

    public function testSetRouteRequirement()
    {
        $app = new Application();

        $app->match('/', function (Request $request) {
            return new Response('ok', 201);
        })
        ->setRequirement('_method', 'POST');

        $request = Request::create('/', 'POST');
        $this->assertResponse($app, $request, 'ok', 201);
    }

    /** @test */
    public function controllerShouldNotRequireRequestTypeHint()
    {
        $app = new Application();

        $app->get('/', function ($request) {
            return new Response('ok', 200);
        });

        $request = Request::create('/');
        $this->assertResponse($app, $request, 'ok', 200);
    }

    /** @test */
    public function controllerShouldConvertStringToResponse()
    {
        $app = new Application();

        $app->get('/', function (Request $request) {
            return 'ok';
        });

        $request = Request::create('/');
        $this->assertResponse($app, $request, 'ok', 200);
    }

    private function assertResponse(Application $app, Request $request, $content, $status)
    {
        $kernel = $app->getContainer()->get('http_kernel');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame($content, $response->getContent());
        $this->assertSame($status, $response->getStatusCode());
    }
}
