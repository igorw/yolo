<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

class ServiceControllerExtension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container
            ->register('controller_resolver.service', 'Yolo\Controller\ServiceControllerResolver')
            ->setArguments([
                new Reference('controller_resolver'),
                new Reference('service_container'),
            ])
            ->addTag('controller_resolver.decorator', []);
    }
}
