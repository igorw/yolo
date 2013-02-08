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

    public function getContainer()
    {
        return $this->container;
    }

    public function get($path, $controller)
    {
        $this->container->get('route_builder')->get($path, $controller);
    }

    public function post($path, $controller)
    {
        $this->container->get('route_builder')->post($path, $controller);
    }

    public function put($path, $controller)
    {
        $this->container->get('route_builder')->put($path, $controller);
    }

    public function delete($path, $controller)
    {
        $this->container->get('route_builder')->delete($path, $controller);
    }

    public function match($path, $controller, $method = null)
    {
        $this->container->get('route_builder')->match($path, $controller, $method);
    }

    public function run()
    {
        $front = $this->container->get('front_controller');
        $front->run();
    }
}
