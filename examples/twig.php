<?php

require __DIR__.'/../vendor/autoload.php';

$container = Yolo\createContainer(
    [
        'twig.path'     => __DIR__.'/views',
    ],
    [
        new Yolo\Extension\TwigExtension(),
    ]
);

$app = new Yolo\Application($container);

$app->get('/', function ($request) use ($container) {
    $twig = $container->get('twig');

    return $twig->render('index.html.twig');
});

$app->run();
