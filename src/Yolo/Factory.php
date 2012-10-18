<?php

namespace Yolo;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Yolo\Compiler\EventSubscriberPass;

class Factory
{
    public static function createContainer(array $parameters = [])
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yml');

        $container->getParameterBag()->add($parameters);

        $container->getCompiler()->addPass(new EventSubscriberPass());
        $container->compile();

        return $container;
    }
}
