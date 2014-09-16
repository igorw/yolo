<?php

require __DIR__.'/../vendor/autoload.php';

yolo\yolo(function (yolo\Request $request) {
    return new yolo\Response('YOLO');
});
