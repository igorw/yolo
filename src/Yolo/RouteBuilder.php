<?php

namespace Yolo;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RouteBuilder
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function get($name, $path, $controller)
    {
        $this->match($name, $path, $controller, 'GET');
    }

    public function post($name, $path, $controller)
    {
        $this->match($name, $path, $controller, 'POST');
    }

    public function put($name, $path, $controller)
    {
        $this->match($name, $path, $controller, 'PUT');
    }

    public function delete($name, $path, $controller)
    {
        $this->match($name, $path, $controller, 'DELETE');
    }

    public function match($name, $path, $controller, $method = null)
    {
        $requirements = $method ? ['_method' => $method] : [];
        $this->routes->add($name, new Route($path, ['_controller' => $controller], $requirements));
    }
}
