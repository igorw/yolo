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
    'list' => 'yolo\y',
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
// Upside down λ i.e. inverse lambda function
function y($param = NULL, ...$params) {
    if (func_num_args() == 0) {
        return NULL;
    }
    // take the yolo pill and you will see how far the rabbit hole goes
    return cons::cons($param, empty($params) ? NULL : y(...$params));
}

function yolisp($swag, array $env = []) { 
    static $OP_CACHE = []; // HAH! Take that Zend!

    if (!$swag instanceof cons) {
        // implicitly quote non-strings
        if (!is_string($swag)) {
            return $swag;
        }

        // lookup in environment
        if (isset($env[$swag])) {
            return $env[$swag];
        } else if (isset($OP_CACHE[$swag])) {
            return $OP_CACHE[$swag];
        } else if (array_key_exists($swag, DEFAULT_ENV)) {
            $callable = DEFAULT_ENV[$swag];
            if (is_array($callable)) {
                return $OP_CACHE[$swag] = (new \ReflectionMethod(...$callable))->getClosure();
            } else if (is_string($callable)) {
                return $OP_CACHE[$swag] = (new \ReflectionFunction($callable))->getClosure();
            } else {
                return $callable;
            }
        } else if (function_exists($swag)) {
            return $OP_CACHE[$swag] = (new \ReflectionFunction($swag))->getClosure();
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

    $eval = function ($swag) use (&$env) {
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
        case 'let':
            $pairs = cons::car($args);
            $body = cons::car(cons::cdr($args));
            while (!is_null($pairs)) {
                $pair = cons::car($pairs); // (name value) 2-element list
                $env[cons::car($pair)] = $eval(cons::car(cons::cdr($pair)));
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
        // -> and :: aren't normal ops as property name is implicitly quoted
        case '->':
        case '::':
            $obj = $eval(cons::car($args));
            $prop = cons::car(cons::cdr($args));
            if (property_exists($obj, $prop)) {
                if ($command === '->') {
                    return $obj->$prop;
                } else {
                    // this is really ugly syntax for a variable static property access
                    // luckily yolo users don't need to deal with it
                    return $obj::${$prop};
                }
            // PHP has separate symbol tables for methods and properties
            // NOT IN YOLISP!
            } else if (method_exists($obj, $prop)) {
                $method = new \ReflectionMethod($obj, $prop);
                if ($command === '->') {
                    return $method->getClosure($obj);
                } else {
                    return $method->getClosure();
                }
            } else {
                throw new \Exception("No property/method $command$prop in $obj");
            }
            break;
        case 'new':
            $class = cons::car($args);
            $constructor_args = cons::cdr($args);
            $class_name = $eval($class);
            $evaluated_args = array_map($eval, x($constructor_args));
            return new $class_name(...$evaluated_args);
        default:
            $func = $eval($command);
            $evaluated_args = array_map($eval, x($args));
            return $func(...$evaluated_args);
    }
}
