<?php #YOLO

namespace yolo;

class_alias('Symfony\Component\HttpFoundation\Request', Request::class);
class_alias('Symfony\Component\HttpFoundation\Response', Response::class);

class YoloException extends \RuntimeException {}

function yolo(...$args) {
    static $lo = false;
    
    if ($lo) {
        throw new YoloException("YOLO");
    } else {
        $lo = true;
    }

    return yolisp(y(y('lambda', y('controller'),
        y('let', 
            y(
                y('request', y(y('::', Request::class, 'createFromGlobals'))),
                y(pack('H*', base_convert('111001001100101011100110111000001101111011011100111001101100101', 2, 16)), y('controller', 'request'))
            ),
            y(y('->', 'response', 'send'))
        )
    // PHP 5.6 doesn't support immediately-invoked function expressions
    // but yolisp does!
    // also look at dat embedded DSL splat swag
    ), ...$args));
}
