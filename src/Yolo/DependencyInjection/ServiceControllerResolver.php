<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ServiceControllerResolver implements ControllerResolverInterface
{
    const SERVICE_PATTERN = "/[A-Za-z0-9\._\-]+:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    protected $resolver;
    protected $container;

    public function __construct(ControllerResolverInterface $resolver, ContainerInterface $container)
    {
        $this->resolver = $resolver;
        $this->container = $container;
    }

    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller', null);

        if (!is_string($controller) || !preg_match(static::SERVICE_PATTERN, $controller)) {
            return $this->resolver->getController($request);
        }

        list($service, $method) = explode(':', $controller, 2);

        if (!$this->container->has($service)) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return array($this->container->get($service), $method);
    }

    public function getArguments(Request $request, $controller)
    {
        return $this->resolver->getArguments($request, $controller);
    }
}
