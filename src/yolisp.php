<?php #YOLO

namespace yolo;

// $ is a placeholder for an operand
// ? is a placeholder for an optional operand
const OPS = [
    '**' => '$ ** $',
    '!'  => '! $',
    '*'  => '$ * $',
    '/'  => '$ / $',
    '%'  => '$ % $',
    '+'  => '? + $',
    '-'  => '? - $',
    '<<' => '$ << $',
    '>>' => '$ >> $',
    '<'  => '$ < $',
    '<=' => '$ <= $',
    '>'  => '$ > $',
    '>=' => '$ >= $',
    '==' => '$ === $', // because loose comparison is literally the antichrist
    '!=' => '$ !== $',
    '&'  => '$ & $',
    '^'  => '$ ^ $',
    '|'  => '$ | $',
    '&&' => '$ && $',
    '||' => '$ || $',
    // no assignment ops because, immutability, mang
];

const DEFAULT_ENV = [
    'car'  => 'yolo\car',
    'cdr'  => 'yolo\cdr',
    'cons' => 'yolo\cons',
    'nil'  => NULL
];

function car(array $cell) {
    return $cell[0];
}

function cdr(array $cell) {
    return isset($cell[1]) ? $cell[1] : NULL;
}

function cons($first, $rest) {
    return [$first, $rest];
}

function lisprint($item) {
    if (is_array($item)) {
        echo "(";
        $first = true;
        while ($item !== NULL) {
            if ($first) {
                $first = false;
            } else {
                echo " ";
            }
            lisprint(car($item));
            $item = cdr($item);
        }
        echo ")";
    } else if (is_null($item)) {
        echo 'nil';
    } else {
        echo $item;
    }
}

// Unwinds a yolisp list into an array
function x(array $list) {
    $array = [];
    while ($list !== NULL) {
        $array[] = car($list);
        $list = cdr($list);
    }
    return $array;
}

// Makes a yolisp list from the parameters
function y($param, ...$params) {
    // take the yolo pill and you will see how far the rabbit hole goes
    return cons($param, empty($params) ? NULL : y(...$params));
}

// Quotes a value for you
function q($value) {
    return y('quote', $value);
}

// $env is an associative array, not a list of cons cells
function yolisp($swag, array $env = NULL) { 
    static $OP_CACHE = []; // HAH! Take that Zend!

    if ($env === NULL) {
        $env = DEFAULT_ENV;
    }

    if (!is_array($swag)) {
        // implicitly quote numbers
        if (is_int($swag) || is_float($swag)) {
            return $swag;
        }

        // lookup in environment
        if (isset($env[$swag])) {
            return $env[$swag];
        } else if (function_exists($swag)) {
            return $swag;
        // we do class lookup after function lookup because everyone knows functional programming is superior
        // what did you expect? this is yolisp, not yojava
        } else if (class_exists($swag)) {
            return $swag;
        } else if (array_key_exists($swag, OPS)) {
            $format = OPS[$swag];
            $ops = substr_count($format, '$');
            $ops += $optional_ops = substr_count($format, '?');
           
            if ($ops === 0) {
                throw new \Exception("Invalid operator format string: \"$format\"");
            }

            $param_names = [];
            for ($i = 0; $i < $ops; $i++) {
                $param_names[] = '$op' . $i;
            }

            $param_list = '';
            for ($i = 0; $i < $ops; $i++) {
                if ($i !== 0) {
                    $param_list .= ', ';
                }
                $param_list .= $param_names[$i];
                if ($i >= $ops - $optional_ops) {
                    $param_list .= ' = NULL';
                }
            }

            $parts = explode(' ', $format);
            if ($optional_ops) {
                $optionless_expr = '';
                $i = 0;
                foreach ($parts as $part) {
                    if ($part === '?') {
                        $optionless_expr .= ' ';
                    } else if ($part === '$') {
                        $optionless_expr .= ' ' . $param_names[$i];
                        $i++;
                    } else {
                        $optionless_expr .= ' ' . $part;
                    }
                }
            }

            $expr = '';
            $i = 0;
            foreach ($parts as $part) {
                if ($part === '?' || $part === '$') {
                    $expr .= ' ' . $param_names[$i];
                    $i++;
                } else {
                    $expr .= ' ' . $part;
                }
            }

            if ($optional_ops) {
                $body = "if (func_num_args() < $ops) { return $optionless_expr; } else { return $expr; }";
            } else {
                $body = "return $expr;";
            }

            // And people said eval() and create_function() were evil!
            return $OP_CACHE[$swag] = create_function($param_list, $body);
        } else {
            throw new \Exception("Could not find $swag in environment");
        }
    }

    $eval = function ($swag) use ($env) {
        return yolisp($swag, $env);
    };

    list($command, $args) = $swag;
    switch ($command) {
        case 'quote':
            return car($args);
        case 'lambda':
            list($arg_names, list($body)) = $args;
            return function (...$args) use ($arg_names, $body, $env) {
                foreach ($arg_names as $i => $arg_name) {
                    $env[$arg_name] = $args[$i];
                }
                return yolisp($body, $env);
            };
        case 'new':
            list($class, $constructor_args) = $args;
            $class_name = $eval($class);
            $evaluated_args = array_map($eval, x($constructor_args));
            return new $class_name(...$evaluated_args);
        default:
            $func = $eval($command);
            $evaluated_args = array_map($eval, x($args));
            return $func(...$evaluated_args);
    }
}
