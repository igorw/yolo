<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Zend\Mvc\Router\Http\Literal;

class ZendRouterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container
            ->register('url_matcher', 'Yolo\DependencyInjection\ZendUrlMatcher')
            ->addArgument(new Reference('routes'))
            ->addArgument(new Reference('request_context'));
    }
}
