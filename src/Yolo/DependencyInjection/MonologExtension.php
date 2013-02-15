<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

class MonologExtension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container
            ->register('logger', 'Monolog\Logger')
            ->setArguments(['%yolo.name%']);
    }
}
