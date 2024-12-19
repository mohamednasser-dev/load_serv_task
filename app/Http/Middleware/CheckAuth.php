<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check()) {
            return $next($request);
        }else{
            return msg(false,'Unauthenticated',Response::HTTP_UNAUTHORIZED);
        }

    }
}
