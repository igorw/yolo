<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = Yolo\createContainer([
    'debug' => true,
]);
$app = new Yolo\Application($container);

$app->get('hello', '/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$app->get('explosion', '/explosion', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

$app->run();
