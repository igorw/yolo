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

    public function testSetRequirement()
    {
        $app = new Application();

        $app->match('/', function (Request $request) {
            return new Response('ok', 201);
        })
        ->setRequirement('_method', 'POST');

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/', 'POST');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }
}
