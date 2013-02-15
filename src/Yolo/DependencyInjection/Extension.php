<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Yolo\Config\Configuration;

abstract class Extension extends ConfigurableExtension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->getAlias());

        $this->configureRootNode($rootNode);

        return new Configuration($treeBuilder);
    }

    abstract protected function configureRootNode($rootNode);
}
