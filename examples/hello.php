<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = Yolo\createContainer(
    [
        'debug' => true,
    ],
    [
        new Yolo\Extension\MonologExtension(),
    ]
);

$app = new Yolo\Application($container);

$app->get('/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$app->run();
