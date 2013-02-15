<?php

namespace Yolo;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Yolo\Compiler\EventSubscriberPass;
use Yolo\Compiler\ControllerResolverDecoratorPass;
use Yolo\DependencyInjection\YoloExtension;

class ContainerBuilder
{
    private $container;
    private $configDirs = [];

    public function __construct()
    {
        $this->container = new SymfonyContainerBuilder();
        $this->container->registerExtension(new YoloExtension());
        $this->container->loadFromExtension('yolo');
    }

    public function configDir($configDir)
    {
        $this->configDirs[] = $configDir;

        return $this;
    }

    public function registerExtension(ExtensionInterface $extension)
    {
        $this->container->registerExtension($extension);
        $this->container->loadFromExtension($extension->getAlias());

        return $this;
    }

    public function configure($name, array $config)
    {
        $this->container->loadFromExtension($name, $config);

        return $this;
    }

    public function getContainer()
    {
        $locator = new FileLocator($this->configDirs);
        $loader = new YamlFileLoader($this->container, $locator);
        $loader->load('config.yml');

        $this->container->addCompilerPass(new EventSubscriberPass());
        $this->container->addCompilerPass(new ControllerResolverDecoratorPass());
        $this->container->compile();

        return $this->container;
    }
}
