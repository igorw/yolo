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

// lowercase class name for maximum #swag
final class cons {
    private $car, $cdr;

    private function __construct() {}

    public static function cons($car, $cdr) {
        $cons = new cons;
        $cons->car = $car;
        $cons->cdr = $cdr;
        return $cons;
    }

    public static function car(cons $cons) {
        return $cons->car;
    }

    public static function cdr(cons $cons) {
        return $cons->cdr;
    }
}

const DEFAULT_ENV = [
    'car'  => ['yolo\cons', 'car'],
    'cdr'  => ['yolo\cons', 'cdr'],
    'cons' => ['yolo\cons', 'cons'],
    'nil'  => NULL
];

function lisprint($item) {
    if ($item instanceof cons) {
        echo "(";
        $first = true;
        while ($item !== NULL) {
            if ($first) {
                $first = false;
            } else {
                echo " ";
            }
            lisprint(cons::car($item));
            $item = cons::cdr($item);
        }
        echo ")";
    } else if (is_null($item)) {
        echo 'nil';
    } else {
        echo $item;
    }
}

// Unwinds a yolisp list into an array
function x(cons $list = NULL){
    $array = [];
    while ($list !== NULL) {
        $array[] = cons::car($list);
        $list = cons::cdr($list);
    }
    return $array;
}

// Makes a yolisp list from the parameters
// Upside down λ i.e. reverse lambda function
function y($param, ...$params) {
    // take the yolo pill and you will see how far the rabbit hole goes
    return cons::cons($param, empty($params) ? NULL : y(...$params));
}

function yolisp($swag, array $env = NULL) { 
    static $OP_CACHE = []; // HAH! Take that Zend!

    if ($env === NULL) {
        $env = DEFAULT_ENV;
    }

    if (!$swag instanceof cons) {
        // implicitly quote non-strings
        if (!is_string($swag)) {
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

    $command = cons::car($swag);
    $args = cons::cdr($swag);
    switch ($command) {
        case 'quote':
            return cons::car($args);
        case 'lambda':
            $arg_names = cons::car($args);
            $body = cons::car(cons::cdr($args));
            return function (...$args) use ($arg_names, $body, $env) {
                foreach (x($arg_names) as $i => $arg_name) {
                    $env[$arg_name] = $args[$i];
                }
                return yolisp($body, $env);
            };
        case 'new':
            $class = cons::car($args);
            $constructor_args = cons::car(cons::cdr($args));
            $class_name = $eval($class);
            $evaluated_args = array_map($eval, x($constructor_args));
            return new $class_name(...$evaluated_args);
        case 'let':
            $pairs = cons::car($args);
            $body = cons::car(cons::cdr($args));
            while (!is_null($pairs)) {
                // we use a cons not a 2-element list because 2-element lists are stupid
                // seriously why waste an extra cons when you don't need it
                // (a . b) >>>> (a . (b . nil))
                // yolisp officially more efficient than Scheme
                $pair = cons::car($pairs);
                $env[cons::car($pair)] = $eval(cons::cdr($pair));
                $pairs = cons::cdr($pairs);
            }
            return yolisp($body, $env);
        case 'if':
            $expr = cons::car($args);
            $results = cons::cdr($args);
            $on_true = cons::car($results);
            $on_false = cons::car(cons::cdr($results));
            if ($eval($expr)) {
                return $eval($on_true);
            } else {
                return $eval($on_false);
            }
            break;
        default:
            $func = $eval($command);
            $evaluated_args = array_map($eval, x($args));
            return $func(...$evaluated_args);
    }
}
