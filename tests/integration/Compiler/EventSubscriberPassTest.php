<?php

namespace integration;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Yolo\Compiler\EventSubscriberPass;

class EventSubscriberPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->register('dispatcher', 'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher')
                  ->addArgument(new Reference('service_container'));
        $container->register('listener.foo', 'integration\FooListener')
                  ->addTag('kernel.event_subscriber', []);

        $pass = new EventSubscriberPass();
        $pass->process($container);

        $dispatcher = $container->get('dispatcher');
        $this->assertFalse(FooListener::$created);

        $dispatcher->dispatch('kernel.foo');
        $this->assertTrue(FooListener::$created);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Service "listener.bar" must implement interface "Symfony\Component\EventDispatcher\EventSubscriberInterface"
     */
    public function testProcessWithInvalidSubscriber()
    {
        $container = new ContainerBuilder();
        $container->register('dispatcher', 'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher')
                  ->addArgument(new Reference('service_container'));
        $container->register('listener.bar', 'integration\BarListener')
                  ->addTag('kernel.event_subscriber', []);

        $pass = new EventSubscriberPass();
        $pass->process($container);
    }
}

class FooListener implements EventSubscriberInterface
{
    public static $created = false;

    public function __construct()
    {
        static::$created = true;
    }

    public function onFoo()
    {
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.foo' => 'onFoo'];
    }
}

class BarListener
{
}
