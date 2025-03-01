<?php

namespace App\Middleware;

use Framework\MiddlewareInterface;
use Framework\Request;
use Framework\RequestHandlerInterface;
use Framework\Response;

class ChangeResponseExample implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $next): Response
    {
        $response = $next->handle($request);
        $response->setBody($response->getBody() . ' hello from the middleware');
        return $response;
    }
}