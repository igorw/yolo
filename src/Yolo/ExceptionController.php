<?php

namespace Yolo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler as DebugExceptionHandler;

class ExceptionController
{
    private $handler;

    public function __construct(DebugExceptionHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(Request $request)
    {
        $exception = $request->attributes->get('exception');

        return $this->handler->createResponse($exception);
    }
}
