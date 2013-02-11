<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $this->expectOutputString('foo');

        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $kernel->expects($this->once())
               ->method('handle')
               ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
               ->will($this->returnValue(new Response('foo')));

        $front = new FrontController($kernel);
        $front->run();
    }

    public function testRunWithTerminableKernel()
    {
        $this->expectOutputString('foo');

        $kernel = $this->getMock('Yolo\TerminableKernel');
        $kernel->expects($this->once())
               ->method('handle')
               ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
               ->will($this->returnValue(new Response('foo')));
        $kernel->expects($this->once())
               ->method('terminate')
               ->with(
                    $this->isInstanceOf('Symfony\Component\HttpFoundation\Request'),
                    $this->isInstanceOf('Symfony\Component\HttpFoundation\Response')
                )
               ->will($this->returnValue(new Response('foo')));

        $front = new FrontController($kernel);
        $front->run();
    }

    public function testRunFunction()
    {
        $this->expectOutputString('foo');

        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $kernel->expects($this->once())
               ->method('handle')
               ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
               ->will($this->returnValue(new Response('foo')));

        run($kernel);
    }
}

abstract class TerminableKernel implements HttpKernelInterface, TerminableInterface {}
