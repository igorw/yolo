<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Yolo\Application();

$app->get('/', function ($request) {
    return "Hallo welt, got swag yo!\n";
});

$app->run();
