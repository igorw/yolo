<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler as DebugExceptionHandler;

class ExceptionHandler
{
    private $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function handle(Request $request)
    {
        $handler = new DebugExceptionHandler($this->debug);
        $exception = $request->get('exception');

        return $handler->createResponse($exception);
    }
}
