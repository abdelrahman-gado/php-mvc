<?php

namespace App\Middleware;

use Framework\MiddlewareInterface;
use Framework\Request;
use Framework\RequestHandlerInterface;
use Framework\Response;

class ChangeRequestExample implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $next): Response
    {
        $request->post = array_map("trim", $request->post);
        $response = $next->handle($request);
        $response->setBody($response->getBody() . ' hello from the middleware');
        return $response;
    }
}