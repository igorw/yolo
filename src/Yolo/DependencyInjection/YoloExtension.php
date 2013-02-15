<?php

namespace Yolo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YoloExtension extends Extension
{
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('services.yml');

        foreach ($config as $name => $value) {
            $container->setParameter($this->getAlias().'.'.$name, $value);
        }
    }

    protected function configureRootNode($rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('debug')->end()
                ->scalarNode('name')->end()
            ->end();
    }
}
