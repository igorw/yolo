<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GracefulException extends \Exception {}

$container = Yolo\createContainer(
    [
        'debug' => true,
    ],
    [
        new Yolo\Extension\MonologExtension(),
    ]
);

$app = new Yolo\Application($container);

$app->get('/500', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

$app->get('/400', function (Request $request) {
    throw new BadRequestHttpException('Janz schlimm.');
});

$app->get('/graceful', function (Request $request) {
    throw new GracefulException('Fuuuuuuu!');
});

$app->error(function ($event) {
    $e = $event->getException();
    if ($e instanceof GracefulException) {
        $message = sprintf("Exception '%s' handled exception gracefully.\n", $e->getMessage());
        $event->setResponse(new Response($message));
    }
});

$app->run();
