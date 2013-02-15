<?php

namespace Yolo\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;

class TwigExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->getParameterBag()->add([
            'twig.options' => [],
        ]);

        $container
            ->register('twig.loader', 'Twig_Loader_Filesystem')
            ->setArguments(['%twig.path%']);

        $container
            ->register('twig', 'Twig_Environment')
            ->setArguments([
                new Reference('twig.loader'),
                '%twig.options%',
            ]);
    }
}
