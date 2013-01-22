<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = Yolo\Factory::createContainer([
    'debug' => true,
]);
$builder = $container->get('route_builder');

$builder->get('hello', '/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$builder->get('error', '/error', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

$front = $container->get('front_controller');
$front->run();
