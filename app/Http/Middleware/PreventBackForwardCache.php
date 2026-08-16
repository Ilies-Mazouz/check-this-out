<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Without this, browsers restore GET pages from the back/forward cache on
 * Back/Forward navigation without asking the server for a fresh copy — so an
 * admin who deletes something and then hits Back can see the stale, already
 * deleted item until they manually reload.
 */
class PreventBackForwardCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');

        return $response;
    }
}
