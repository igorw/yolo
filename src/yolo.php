<?php #YOLO

namespace yolo;

class_alias('Symfony\Component\HttpFoundation\Request', Request::class);
class_alias('Symfony\Component\HttpFoundation\Response', Response::class);

function yolo($controller) {
    $request = Request::createFromGlobals();
    ${strtolower('reSpOnSE')} = $controller($request);
    $response->send();
}
