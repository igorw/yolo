<?php

require __DIR__.'/../vendor/autoload.php';

class HelloController
{
    public function worldAction($request)
    {
        return "Hallo welt, got swag yo!\n";
    }
}

$container = Yolo\createContainer(
    [
        'debug' => true,
    ],
    [
        new Yolo\DependencyInjection\MonologExtension(),
        new Yolo\DependencyInjection\ServiceControllerExtension(),
        new Yolo\DependencyInjection\CallableExtension(
            'controller',
            function ($container) {
                $container->register('hello.controller', 'HelloController');
            }
        ),
    ]
);

$app = new Yolo\Application($container);

$app->get('/', 'hello.controller:worldAction');

$app->run();
