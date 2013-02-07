<?php

namespace Yolo;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Application
{
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container ?: createContainer();
    }

    public function get($name, $path, $controller)
    {
        $this->container->get('route_builder')->get($name, $path, $controller);
    }

    public function post($name, $path, $controller)
    {
        $this->container->get('route_builder')->post($name, $path, $controller);
    }

    public function put($name, $path, $controller)
    {
        $this->container->get('route_builder')->put($name, $path, $controller);
    }

    public function delete($name, $path, $controller)
    {
        $this->container->get('route_builder')->delete($name, $path, $controller);
    }

    public function match($name, $path, $controller, $method = null)
    {
        $this->container->get('route_builder')->match($name, $path, $controller, $method);
    }

    public function run()
    {
        $front = $this->container->get('front_controller');
        $front->run();
    }
}
