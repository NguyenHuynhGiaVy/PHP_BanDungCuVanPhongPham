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
                && $request->user()->role()->where('name', 'ADMIN')->exists(),
            403,
        );

        return $next($request);
    }
}
