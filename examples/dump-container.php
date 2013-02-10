<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

$container = Yolo\createContainer();

$dumper = new PhpDumper($container);
echo $dumper->dump();
