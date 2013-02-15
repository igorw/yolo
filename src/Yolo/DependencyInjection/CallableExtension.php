<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

class CallableExtension extends BaseExtension
{
    private $alias;
    private $callable;

    public function __construct($alias, callable $callable)
    {
        $this->alias = $alias;
        $this->callable = $callable;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        call_user_func($this->callable, $configs, $container);
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
