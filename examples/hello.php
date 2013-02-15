<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = (new Yolo\ContainerBuilder())
    ->registerExtension(new Yolo\DependencyInjection\MonologExtension())
    ->configure('yolo', ['debug' => false])
    ->configDir(__DIR__.'/config')
    ->getContainer();

var_dump($container->getParameterBag()->all());

$app = new Yolo\Application($container);

$app->get('/', function (Request $request) {
    return new Response("Hallo welt, got swag yo!\n");
});

$app->run();
