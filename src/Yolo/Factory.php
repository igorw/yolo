<?php

namespace Yolo;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Yolo\DependencyInjection\YoloExtension;
use Yolo\Compiler\EventSubscriberPass;

class Factory
{
    public static function createContainer(array $parameters = [])
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new YoloExtension());

        $container->getParameterBag()->add($parameters);

        foreach ($container->getExtensions() as $extension) {
            $container->loadFromExtension($extension->getAlias());
        }

        $container->addCompilerPass(new EventSubscriberPass());
        $container->compile();

        return $container;
    }
}
