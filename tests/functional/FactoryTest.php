<?php

namespace functional;

use Yolo;
use Yolo\DependencyInjection\MonologExtension;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateContainer()
    {
        $container = Yolo\createContainer();

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $container);
        $this->assertInstanceOf('Symfony\Component\HttpKernel\HttpKernel', $container->get('http_kernel'));
        $this->assertInstanceOf('Yolo\FrontController', $container->get('front_controller'));
        $this->assertSame(false, $container->getParameter('yolo.debug'));
    }

    /** @test */
    public function testCreateContainerWithParameter()
    {
        $container = Yolo\createContainer(['yolo.debug' => true]);

        $this->assertSame(true, $container->getParameter('yolo.debug'));
    }

    /** @test */
    public function testCreateContainerWithExtension()
    {
        $container = Yolo\createContainer([], [
            new MonologExtension(),
        ]);

        $this->assertInstanceOf('Monolog\Logger', $container->get('logger'));
    }
}
