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
        new Yolo\DependencyInjection\MonologExtension(),
    ]
);
$app = new Yolo\Application($container);

$app->get('explosion', '/500', function (Request $request) {
    throw new \Exception('Holy crap, explosion!');
});

$app->get('bad_request', '/400', function (Request $request) {
    throw new BadRequestHttpException('Janz schlimm.');
});

$app->get('graceful', '/graceful', function (Request $request) {
    throw new GracefulException('Fuuuuuuu!');
});

$container
    ->get('dispatcher')
    ->addListener(KernelEvents::EXCEPTION, function ($event) {
        $e = $event->getException();
        if ($e instanceof GracefulException) {
            $message = sprintf("Exception '%s' handled exception gracefully.\n", $e->getMessage());
            $event->setResponse(new Response($message));
        }
    });

$app->run();
