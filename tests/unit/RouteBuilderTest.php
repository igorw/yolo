<?php

namespace Yolo;

use Symfony\Component\Routing\RouteCollection;

class RouteBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideRouteMethods */
    public function testRouteBuilding($method)
    {
        $routes = new RouteCollection();
        $builder = new RouteBuilder($routes);

        $controller = function () {};
        $route = $builder->$method('/foo', $controller);

        $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);

        $routes = $routes->all();
        $this->assertCount(1, $routes);
        $this->assertSame('/foo', $routes[0]->getPath());
        $this->assertSame([strtoupper($method)], $routes[0]->getMethods());
        $this->assertSame($controller, $routes[0]->getDefaults()['_controller']);
    }

    public function provideRouteMethods()
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
        ];
    }

    public function testMatchWithNoMethod()
    {
        $routes = new RouteCollection();
        $builder = new RouteBuilder($routes);

        $controller = function () {};
        $builder->match('/foo', $controller);

        $routes = $routes->all();
        $this->assertCount(1, $routes);
        $this->assertSame([], $routes[0]->getMethods());
    }

    public function testMatchWithManyMethods()
    {
        $routes = new RouteCollection();
        $builder = new RouteBuilder($routes);

        $controller = function () {};
        $builder->match('/foo', $controller, 'GET|POST');

        $routes = $routes->all();
        $this->assertCount(1, $routes);
        $this->assertSame(['GET', 'POST'], $routes[0]->getMethods());
    }
}
