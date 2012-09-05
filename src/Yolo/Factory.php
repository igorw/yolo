<?php

namespace Yolo;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Factory
{
    public static function createContainer()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yml');

        $dispatcher = $container->get('dispatcher');
        $subscriberIds = $container->findTaggedServiceIds('event_subscriber');
        foreach ($subscriberIds as $subscriberId => $tag) {
            $subscriber = $container->get($subscriberId);
            $dispatcher->addSubscriber($subscriber);
        }

        return $container;
    }
}
