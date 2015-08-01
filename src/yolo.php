<?php #YOLO

namespace yolo;

class_alias('Symfony\Component\HttpFoundation\Request', Request::class);
class_alias('Symfony\Component\HttpFoundation\Response', Response::class);

class YoloException extends \RuntimeException {}

function yolo(...$args) {
    static $lo = false;

    if (!$lo) {
        goto YOLO;
    }
    throw new YoloException("YOLO");

    YOLO: $lo = true;
    return yolisp(y(swagify('
        (lambda (controller)
            (let
                (
                    (request ((:: yolo\request createFromGlobals)))
                    (response (controller request))
                )
                ((-> response send))
            )
        )'
    // PHP 5.6 doesn't support immediately-invoked function expressions
    // but yolisp does!
    // also look at dat embedded DSL splat swag
    ), ...$args));
}
