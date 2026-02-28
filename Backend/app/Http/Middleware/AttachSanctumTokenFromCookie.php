<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachSanctumTokenFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken() && $request->cookies->has('auth_token')) {
            $token = $request->cookies->get('auth_token');
            if ($token) {
                $request->headers->set('Authorization', 'Bearer '.$token);
            }
        }

        return $next($request);
    }
}
