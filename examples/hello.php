<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = Yolo\createContainer(
    [
        'debug' => true,
    ],
    [
        new Yolo\DependencyInjection\MonologExtension(),
    ]
);

$app = new Yolo\Application($container);

$app->get('hello', '/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$app->run();
