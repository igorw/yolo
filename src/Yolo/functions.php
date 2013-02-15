<?php

namespace Yolo;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Yolo\Extension\YoloExtension;
use Yolo\FrontController;
use Yolo\Compiler\EventSubscriberPass;
use Yolo\Compiler\ControllerResolverDecoratorPass;

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
    $container->addCompilerPass(new ControllerResolverDecoratorPass());
    $container->compile();

    return $container;
}

function run(HttpKernelInterface $kernel)
{
    $front = new FrontController($kernel);
    $front->run();
}
