<?php

namespace integration;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Yolo\Dumper\FlatPhpDumper;

class FlatPhpDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpWithEmptyContainer()
    {
        $container = new ContainerBuilder();
        $dumper = new FlatPhpDumper($container);

        $code = $dumper->dump();

        $expected = <<<EOF
<?php


EOF;
        $this->assertSame($expected, $code);
    }

    public function testDumpParameters()
    {
        $container = new ContainerBuilder();
        $container->getParameterBag()->add([
            'app.name' => 'yolo',
            'debug' => false,
        ]);
        $dumper = new FlatPhpDumper($container);

        $code = $dumper->dump();

        $expected = <<<EOF
<?php

\$container->getParameterBag()->add(array(
    'app.name' => 'yolo',
    'debug' => false,
));

EOF;
        $this->assertSame($expected, $code);
    }

    public function testDumpServices()
    {
        $container = new ContainerBuilder();
        $container
            ->register('route_builder', 'Yolo\RouteBuilder')
            ->setArguments([new Reference('routes')]);
        $container
            ->register('routes', 'Symfony\Component\Routing\RouteCollection');
        $dumper = new FlatPhpDumper($container);

        $code = $dumper->dump();

        $expected = <<<EOF
<?php

\$container
    ->register('route_builder', 'Yolo\\\\RouteBuilder')
    ->addArgument(new Reference('routes'));

\$container
    ->register('routes', 'Symfony\\\\Component\\\\Routing\\\\RouteCollection');

EOF;
        $this->assertSame($expected, $code);
    }

    public function testDumpServicesWithAllAttributes()
    {
        $container = new ContainerBuilder();
        $container
            ->register('foo', 'Foo')
            ->addTag('kernel.event_subscriber')
            ->setFile('create_foo.php')
            ->setFactoryMethod('create')
            ->setFactoryService('foo_factory')
            ->setProperties(array('bar' => 'baz'))
            ->setMethodCalls(array(array('onInit', array())))
            ->setScope('request');
        $dumper = new FlatPhpDumper($container);

        $code = $dumper->dump();

        $expected = <<<EOF
<?php

\$container
    ->register('foo', 'Foo')
    ->addTag('kernel.event_subscriber', array(

    ))
    ->setFile('create_foo.php')
    ->setFactoryMethod('create')
    ->setFactoryService('foo_factory')
    ->setProperty('bar', 'baz')
    ->addMethodCall('onInit', array())
    ->setScope('request');

EOF;
        $this->assertSame($expected, $code);
    }
}
