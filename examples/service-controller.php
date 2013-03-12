<?php

require __DIR__.'/../vendor/autoload.php';

class HelloController
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function worldAction($request)
    {
        return "Hello, I'm {$this->name}.\n";
    }
}

$container = Yolo\createContainer(
    [
        'hello.name' => 'the amazing app',
    ],
    [
        new Yolo\DependencyInjection\ServiceControllerExtension(),
        new Yolo\DependencyInjection\CallableExtension(
            'controller',
            function ($configs, $container) {
                $container->register('hello.controller', 'HelloController')
                          ->addArgument('%hello.name%');
            }
        ),
    ]
);

$app = new Yolo\Application($container);

$app->get('/', 'hello.controller:worldAction');

$app->run();
