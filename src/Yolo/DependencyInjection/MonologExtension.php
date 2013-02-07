<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MonologExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $container
            ->register('logger', 'Monolog\Logger')
            ->setArguments(['%app.name%']);
    }
}
