<?php

namespace Yolo;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Yolo\Compiler\EventSubscriberPass;

class Factory
{
    public static function createContainer(array $parameters = null)
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yml');

        if ($parameters) {
            foreach ($parameters as $name => $value) {
                $container->setParameter($name, $value);
            }
        }

        $compiler = new Compiler();
        $compiler->addPass(new EventSubscriberPass());
        $compiler->compile($container);

        return $container;
    }
}
