<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ServiceControllerExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $container
            ->register('controller_resolver.service', 'Yolo\DependencyInjection\ServiceControllerResolver')
            ->setArguments([
                new Reference('controller_resolver.decorated'),
                new Reference('service_container'),
            ])
            ->addTag('controller_resolver.decorator', []);
    }
}
