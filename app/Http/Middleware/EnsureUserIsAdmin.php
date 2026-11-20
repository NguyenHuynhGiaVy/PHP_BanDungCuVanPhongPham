<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->status === 1
                && in_array($request->user()->role?->name, ['ADMIN', 'STAFF'], true),
            403,
        );

        return $next($request);
    }
}
