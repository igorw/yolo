<?php #YOLO

namespace 😎;

class_alias('Symfony\Component\HttpFoundation\Request', Request::class);
class_alias('Symfony\Component\HttpFoundation\Response', Response::class);

function yolo($controller) {
    $request = Request::createFromGlobals();
    $response = $controller($request);
    $response->send();
}
