<?php

namespace functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Yolo;
use Yolo\Application;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function errorListenerShouldCatchErrors()
    {
        // use debug logger to avoid error_log calls
        $container = Yolo\createContainer([], [
            new DebugLoggerExtension(),
        ]);

        $app = new Application($container);

        $app->get('/', function (Request $request) {
            throw new \Exception('drama');
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertContains('Whoops, looks like something went wrong.', $response->getContent());
    }

    /** @test */
    public function customErrorListenerShouldFireBeforeGlobalOne()
    {
        $app = new Application();

        $app->error(function (GetResponseForExceptionEvent $event) {
            $e = $event->getException();
            $event->setResponse(new Response($e->getMessage()));
        });

        $app->get('/', function (Request $request) {
            throw new \Exception('drama');
        });

        $kernel = $app->getContainer()->get('http_kernel');
        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('drama', $response->getContent());
    }
}

use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Psr\Log\NullLogger;

class DebugLogger extends NullLogger implements DebugLoggerInterface
{
    public function getLogs()
    {
    }

    public function countErrors()
    {
    }
}

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DebugLoggerExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $container->register('logger', 'functional\DebugLogger');
    }
}
