<?php

namespace Yolo\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class EventSubscriberPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('dispatcher');

        foreach ($container->findTaggedServiceIds('kernel.event_subscriber') as $id => $attributes) {
            $definition->addMethodCall('addSubscriber', array(new Reference($id)));
        }
    }
}
