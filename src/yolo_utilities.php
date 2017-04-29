<?php #YOLO

namespace yolo;



/**
 * May, or may not, do what you ask
 * @param  Callable $fn          What you want to do
 * @param  float    $probability between 0 and 1 that it'll happen
 * @return Callable              What you want to do, or not.
 */
function maybe (Callable $fn, $probability) {
    return rand(0,1) < $probability ? $fn : function () {};
}

/**
 * Ignores you completely
 * @param  Callable $fn What you want to do
 * @return Callable     Not what you want to do
 */
function whatever (Callable $fn) {
    return function () {};
}


/**
 * Marginally increase the chance that what you want, will happen
 * @param  Callable $fn What you want to do
 * @return Callable     A maybe: give it a probability and it might do it
 */
function bribe (Callable $fn) {
    return function ($probability) use ($fn) {
        return maybe($fn, $probability + 0.1);
    };
}


/**
 * Hormones
 * @return
 * @throws \Exception
 */
function strop () {
    echo "I'M GOING OUT";
    throw new \Exception("It's just a phase");
}


/**
 * Randomly changes the order of the parameters of your function
 * @param  Callable $fn What you want to do
 * @return Callable     What you want to do, but possibly a bit messed up
 */
function just_messin_wit_u (Callable $fn) {
    return function () use ($fn) {
        $args = func_get_args();
        shuffle($args);
        return call_user_func_array($fn, $args);
    };
}
