<?php

namespace Yolo;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Yolo\DependencyInjection\YoloExtension;
use Yolo\Compiler\EventSubscriberPass;

function createContainer(array $parameters = [], array $extensions = [])
{
    $container = new ContainerBuilder();
    $container->registerExtension(new YoloExtension());
    foreach ($extensions as $extension) {
        $container->registerExtension($extension);
    }

    $container->getParameterBag()->add($parameters);

    foreach ($container->getExtensions() as $extension) {
        $container->loadFromExtension($extension->getAlias());
    }

    $container->addCompilerPass(new EventSubscriberPass());
    $container->compile();

    return $container;
}
