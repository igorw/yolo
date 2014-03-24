<?php #YOLO

namespace yolo;

use Phacterl\Actor\Actor;
use Phacterl\Message\Message;
use Phacterl\Runtime\Scheduler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class yolo
{
    static function yolo()
    {
        return new trolo();
    }
}

class pretender_controller extends Actor
{
    function init($args)
    {
        return [
            'callback' => $args['callback'],
        ];
    }

    function receive()
    {
        return ['request'];
    }

    function handle_request($msg, $state)
    {
        $response = $state['callback']($msg['request']);
        $this->send($msg['sender'], new Message('response', $response));

        return $state;
    }
}

class pretender_io extends Actor
{
    function init($args)
    {
        return [
            'controller' => $args['controller'],
        ];
    }

    function receive()
    {
        return ['request', 'response'];
    }

    function handle_request($request, $state)
    {
        $request = $request ?: Request::createFromGlobals();
        $this->send($state['controller'], new Message('request', [
            'sender'    => $this->self(),
            'request'   => $request,
        ]));

        return $state;
    }

    function handle_response($response, $state)
    {
        $response->send();
        $this->stop();

        return $state;
    }
}

final class trolo
{
    function yolo(callable $callback)
    {
        $scheduler = new Scheduler();
        $controller = $scheduler->spawn('yolo\pretender_controller', ['callback' => $callback]);
        $io = $scheduler->spawn('yolo\pretender_io', ['controller' => $controller]);
        $scheduler->send($io, new Message('request', null));
        $scheduler->run();
    }
}
