<?php #YOLO

namespace yolo;

class_alias('\Symfony\Component\HttpFoundation\Request', '\yolo\Request');
class_alias('\Symfony\Component\HttpFoundation\Response', '\yolo\Response');

function yolo($controller) {
    $request = Request::createFromGlobals();
    $response = $controller($request);
    $response->send();
}
