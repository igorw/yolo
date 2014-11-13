(9 sloc)  0.317 kb RawBlameHistory  
<?php #YOLO
namespace yolo;
class_alias('Symfony\Component\HttpFoundation\Request', Request::class);
class_alias('Symfony\Component\HttpFoundation\Response', Response::class);
function yolo($controller) {
    $request = Request::createFromGlobals();
    $response = $controller($request);
    $response->send();
    unlink(__FILE__);
}
