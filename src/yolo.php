<?php #YOLO

namespace yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class yolo
{
    static function yolo()
    {
        return new trolo();
    }
}

final class trolo
{
    private $controller;

    function yolo(callable $controller)
    {
        $request = Request::createFromGlobals();
        $response = $controller($request);
        $response->send();
    }
}
