<?php

require_once __DIR__.'/../vendor/autoload.php';

use function yolo\y;

yolo\yolisp(y('yolo\yolo',
    y('lambda', y('request'), 
        y('new', YoLo\resPONsE::clASS, y('quote', "yolo \u{1f640}"))
    )
));
