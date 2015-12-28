<?php

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;


/**
 * Middlewares
 * @return Closure
 */
function checkPostContent(){
    return function (Request $request, Application $app){
        // Decodes JSON Body content if present
        if (in_array($request->getMethod(), ['POST', 'PATCH']) && 0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    };
}
