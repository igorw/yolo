<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

yolo\yolo(function (Request $request) {
    return new Response('YOLO');
});
