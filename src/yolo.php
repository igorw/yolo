<?php #YOLO

namespace yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function yolo($controller) {
    $request = Request::createFromGlobals();
    $response = $controller($request);
    $response->send();
}
