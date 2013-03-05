<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require __DIR__.'/vendor/autoload.php';

$configDir = __DIR__.'/config';
$target = $configDir.'/services.php';

$container = new ContainerBuilder();

$loader = new YamlFileLoader($container, new FileLocator($configDir));
$loader->load('services.yml');

$dumper = new Yolo\Dumper\FlatPhpDumper($container);

$code = $dumper->dump();
echo $code;
// file_put_contents($target, $code);
