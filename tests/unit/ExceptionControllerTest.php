<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $e = new \RuntimeException('foo');
        $request = Request::create('/');
        $request->attributes->set('exception', $e);

        $response = new Response('bar', 500);

        $handler = $this->getMockBuilder('Symfony\Component\HttpKernel\Debug\ExceptionHandler')
                        ->disableOriginalConstructor()
                        ->getMock();
        $handler->expects($this->once())
                ->method('createResponse')
                ->with($e)
                ->will($this->returnValue($response));

        $controller = new ExceptionController($handler);
        $this->assertSame($response, $controller->handle($request));
    }
}
