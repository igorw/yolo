<?php

// The **Application** object is a facade to the *RouteBuilder*, the
// *EventDispatcher* and the *FrontController*.
//
// It provides convenience methods for the most common functionality.

namespace Yolo;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class Application
{
    const EARLY_EVENT = 512;
    const LATE_EVENT  = -512;

    private $container;

    // You can optionally pass in a service container to the constructor. If
    // you don't pass one, a standard one will be created via the
    // *Yolo\createContainer()* function.
    //
    // A sample invokation:
    //
    //     $app = new Yolo\Application();
    //
    // Or if you want to pass a custom container:
    //
    //     $container = Yolo\createContainer([
    //         'debug' => true,
    //     ]);
    //
    //     $app = new Yolo\Application($container);

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container ?: createContainer();
    }

    public function getContainer()
    {
        return $this->container;
    }

    // For each of the standard HTTP methods, there is a corresponding
    // convenience method on the *Application*. It will proxy to the
    // *RouteBuilder*.
    //
    // These methods allow you to define routes, which basically map path
    // patterns to controller functions.
    //
    // A controller function takes a *Request* argument and returns a
    // *Response* object.
    //
    // Sample usage:
    //
    //     use Symfony\Component\HttpFoundation\Request;
    //     use Symfony\Component\HttpFoundation\Response;
    //
    //     $app->get('/', function (Request $request) {
    //         return Response('Hi.');
    //     });
    //
    //     $app->post('/foo', function (Request $request) {
    //         return Response('Created successfully.', 201, [
    //             'Location' => '/foo/id'
    //         ]);
    //     });

    public function get($path, $controller)
    {
        return $this->container->get('route_builder')->get($path, $controller);
    }

    public function post($path, $controller)
    {
        return $this->container->get('route_builder')->post($path, $controller);
    }

    public function put($path, $controller)
    {
        return $this->container->get('route_builder')->put($path, $controller);
    }

    public function delete($path, $controller)
    {
        return $this->container->get('route_builder')->delete($path, $controller);
    }

    public function match($path, $controller, $method = null)
    {
        return $this->container->get('route_builder')->match($path, $controller, $method);
    }

    // The *before*, *after* and *error* methods are simple proxies to the
    // *EventDispatcher*.
    //
    // They allow you to register listeners for certain events.
    //
    // Sample usage:
    //
    //     $app->before(function ($event) {
    //         $request = $event->getRequest();
    //         if ('::1' !== $request->getClientIp()) {
    //             $response = new Response('Only localhost allowed.', 403);
    //             $event->setResponse($response);
    //         }
    //     });
    //
    //     $app->after(function ($event) {
    //         $response = $event->getResponse();
    //         $response->headers->set('igor-was-here', 'true');
    //     });

    public function before($listener, $priority = 0)
    {
        $this->container->get('dispatcher')->addListener(KernelEvents::REQUEST, $listener, $priority);
    }

    public function after($listener, $priority = 0)
    {
        $this->container->get('dispatcher')->addListener(KernelEvents::RESPONSE, $listener, $priority);
    }

    public function error($listener, $priority = 0)
    {
        $this->container->get('dispatcher')->addListener(KernelEvents::EXCEPTION, $listener, $priority);
    }

    // The *run* method provides a shortcut to serve the application to a
    // browser. You should call this from the front controller.
    //
    // Sample usage:
    //
    //     $app->run();

    public function run()
    {
        $front = $this->container->get('front_controller');
        $front->run();
    }
}
