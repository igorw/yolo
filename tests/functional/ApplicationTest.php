<?php

namespace functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yolo\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $app = new Application();

        $app->get('/', function (Request $request) {
            return new Response("Hallo welt, got swag yo!\n");
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertSame("Hallo welt, got swag yo!\n", $response->getContent());
    }
}
