<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Zend\Http\Request;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\Http\Literal;

class ZendUrlMatcher implements UrlMatcherInterface
{
    private $routes;
    private $context;
    private $routeStack;

    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        $this->routes = $routes;
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $this->routeStack = $this->routeStack ?: $this->populateRouteStack();

        $zendRequest = new Request();
        $zendRequest->setMethod($this->context->getMethod());
        $zendRequest->setUri($pathinfo);

        $match = $this->routeStack->match($zendRequest);
        return $match->getParams();
    }

    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    private function populateRouteStack()
    {
        $routeStack = new TreeRouteStack();

        foreach ($this->routes as $name => $route) {
            $zendRoute = Literal::factory([
                'route' => $route->getPath(),
                'defaults' => $route->getDefaults(),
            ]);

            $routeStack->addRoute($name, $zendRoute);
        }

        return $routeStack;
    }
}
