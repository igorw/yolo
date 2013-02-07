<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Yolo\Application();

$app->get('hello', '/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$app->get('error', '/error', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

$app->run();
