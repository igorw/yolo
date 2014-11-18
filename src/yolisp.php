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

function yolisp($swag, array $env = []) {
    static $OP_CACHE = []; // HAH! Take that Zend!

    if (!is_array($swag)) {
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
            list($class, $constructor_args) = $args;
            $class_name = $eval($class);
            $evaluated_args = array_map($eval, $constructor_args);
            return new $class_name(...$evaluated_args);
        default:
            $func = $eval($command);
            $evaluated_args = array_map($eval, $args);
            return $func(...$evaluated_args);
    }
}
