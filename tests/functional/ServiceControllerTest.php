<?php

namespace functional;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Yolo;
use Yolo\Application;

class ServiceControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $container = Yolo\createContainer([],
            [
                new Yolo\Extension\ServiceControllerExtension(),
                new Yolo\Extension\CallableExtension(
                    'controller',
                    function (ContainerInterface $container) {
                        $container->register('hello.controller', 'functional\HelloController');
                    }
                ),
            ]
        );

        $app = new Application($container);
        $app->get('/', 'hello.controller:worldAction');

        $request = Request::create('/');
        $this->assertResponse($app, $request, "Look at me, I'm a service!", 200);
    }

    /** @test */
    public function serviceControllerShouldDelegateForNonServices()
    {
        $container = Yolo\createContainer([],
            [
                new Yolo\Extension\ServiceControllerExtension(),
                new Yolo\Extension\CallableExtension(
                    'controller',
                    function (ContainerInterface $container) {
                        $container->register('hello.controller', 'functional\HelloController');
                    }
                ),
            ]
        );

        $app = new Application($container);
        $app->get('/', 'hello.controller:worldAction');
        $app->get('/foo', function (Request $request) {
            return "I'm not a service.";
        });

        $request = Request::create('/foo');
        $this->assertResponse($app, $request, "I'm not a service.", 200);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedException Service "non_existent.controller" does not exist.
     */
    public function serviceControllerShouldFailOnNonExistentService()
    {
        $container = Yolo\createContainer([],
            [
                new Yolo\Extension\ServiceControllerExtension(),
            ]
        );

        $app = new Application($container);
        $app->get('/', 'non_existent.controller:missingAction');

        $request = Request::create('/');
        $kernel = $app->getContainer()->get('http_kernel');
        $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
    }

    private function assertResponse(Application $app, Request $request, $content, $status)
    {
        $kernel = $app->getContainer()->get('http_kernel');
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertSame($content, $response->getContent());
        $this->assertSame($status, $response->getStatusCode());
    }
}

class HelloController
{
    public function worldAction(Request $request)
    {
        return new Response("Look at me, I'm a service!");
    }
}
