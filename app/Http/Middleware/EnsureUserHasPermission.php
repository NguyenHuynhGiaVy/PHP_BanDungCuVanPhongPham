<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || $user->status !== 1) {
            abort(403, 'Tài khoản không có quyền truy cập hoặc đã bị vô hiệu hóa.');
        }

        if (! $user->hasPermission($permission)) {
            abort(403, 'Bạn không có quyền thực hiện chức năng này ('.$permission.'). Vui lòng liên hệ quản trị viên.');
        }

        return $next($request);
    }
}
