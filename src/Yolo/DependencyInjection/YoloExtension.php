<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YoloExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('services.yml');

        $definitions = [
            'debug'     => ['type' => 'bool'],
            'app.name'  => ['type' => 'string'],
        ];

        foreach ($configs as $config) {
            foreach ($config as $name => $value) {
                if (!isset($definitions[$name])) {
                    throw new \InvalidArgumentException(sprintf("Invalid config option '%s' provided.", $name));
                }

                $def = $definitions[$name];
                $cast = [$this, $def['type'].'val'];
                $container->setParameter($name, $cast($value));
            }
        }
    }

    public function boolval($value)
    {
        return (bool) $value;
    }

    public function stringval($value)
    {
        return (string) $value;
    }

    public function intval($value)
    {
        return (int) $value;
    }

    public function floatval($value)
    {
        return (float) $value;
    }
}
