<?php #YOLO

namespace yolo;

function yolisp($swag, array $env = []) {
    if (!is_array($swag)) {
        if (isset($env[$swag])) {
            return $env[$swag];
        } else if (function_exists($swag)) {
            return $swag;
        } else {
            throw new \Exception("Could not find $swag in environment");
        }
    }

    $command = $swag[0];
    $args = array_slice($swag, 1);
    switch ($command) {
        case 'quote':
            return $args[0];
        case 'lambda':
            list($arg_names, $body) = $args;
            return function (...$args) use ($arg_names, $body, $env) {
                foreach ($arg_names as $i => $arg_name) {
                    $env[$arg_name] = $args[$i];
                }
                return yolisp($body, $env);
            };
        case 'new':
            list($class_name, $constructor_args) = $args;
            $evaluated_args = array_map('yolo\yolisp', $constructor_args);
            return new $class_name(...$evaluated_args);
        default:
            $func = yolisp($command);
            $evaluated_args = array_map('yolo\yolisp', $args);
            return $func(...$evaluated_args);
    }
}
