<?php

namespace Yolo;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Yolo\FrontController;
use Yolo\Compiler\EventSubscriberPass;
use Yolo\Compiler\ControllerResolverDecoratorPass;
use Yolo\DependencyInjection\YoloExtension;

function createContainer(array $parameters = [], array $extensions = [])
{
    $container = new ContainerBuilder();
    $container->getParameterBag()->add($parameters);

    $container->registerExtension(new YoloExtension());
    foreach ($extensions as $extension) {
        $container->registerExtension($extension);
    }

    foreach ($container->getExtensions() as $extension) {
        $container->loadFromExtension($extension->getAlias());
    }

    $container->addCompilerPass(new EventSubscriberPass());
    $container->addCompilerPass(new ControllerResolverDecoratorPass());
    $container->compile();

    return $container;
}

function run(HttpKernelInterface $kernel)
{
    $front = new FrontController($kernel);
    $front->run();
}
