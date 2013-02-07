<?php

namespace Yolo\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ControllerResolverDecoratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setDefinition('controller_resolver.prev', $container->getDefinition('controller_resolver'));
        $prev = 'controller_resolver.prev';

        foreach ($container->findTaggedServiceIds('controller_resolver.decorator') as $id => $attributes) {
            $decorator = $container->getDefinition($id);
            $decorator->replaceArgument(0, new Reference($prev));

            $prev = $id;
        }

        $container->setAlias('controller_resolver', $prev);
    }
}
