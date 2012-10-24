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
        $container->loadFromExtension('yolo');

        $container->getParameterBag()->add($parameters);

        $container->addCompilerPass(new EventSubscriberPass());
        $container->compile();

        return $container;
    }
}
