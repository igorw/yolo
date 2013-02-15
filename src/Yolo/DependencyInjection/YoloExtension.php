<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Yolo\Config\Configuration;

class YoloExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $name => $value) {
            $container->setParameter($this->getAlias().'.'.$name, $value);
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->getAlias());

        $this->configureRootNode($rootNode);

        return new Configuration($treeBuilder);
    }

    public function configureRootNode($rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('debug')->end()
                ->scalarNode('name')->end()
            ->end();
    }
}
