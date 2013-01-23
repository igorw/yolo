<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = Yolo\Factory::createContainer();
$builder = $container->get('route_builder');

$builder->get('hello', '/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$builder->get('error', '/error', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

printf("%-10s | %-10s | %-10s\n", 'method', 'pattern', 'name');

$routes = $container->get('routes');
foreach ($routes as $name => $route) {
    printf(
        "%-10s | %-10s | %-10s\n",
        $route->getRequirement('_method'),
        $route->getPattern(),
        $name
    );
}
