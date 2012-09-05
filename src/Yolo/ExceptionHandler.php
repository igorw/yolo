<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler as DebugExceptionHandler;

class ExceptionHandler
{
    public function handle(Request $request)
    {
        $handler = new DebugExceptionHandler();
        $exception = $request->get('exception');

        return $handler->createResponse($exception);
    }
}
