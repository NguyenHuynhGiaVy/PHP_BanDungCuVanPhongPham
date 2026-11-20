<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::query()
            ->orderBy('sort_order')
            ->get();

        $roles = Role::query()
            ->whereIn('name', ['ADMIN', 'STAFF', 'CUSTOMER'])
            ->with('permissions:id')
            ->get()
            ->keyBy('name');

        return view('admin.permissions.index', [
            'permissions' => $permissions,
            'roles' => $roles,
            'adminRole' => $roles->get('ADMIN'),
            'staffRole' => $roles->get('STAFF'),
            'customerRole' => $roles->get('CUSTOMER'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $matrix = $request->input('matrix', []);

        $roles = Role::query()->whereIn('name', ['ADMIN', 'STAFF', 'CUSTOMER'])->get();
        $allPermissionIds = Permission::pluck('id')->toArray();

        foreach ($roles as $role) {
            if ($role->name === 'ADMIN') {
                // Quản trị viên luôn luôn sở hữu tất cả các quyền
                $role->permissions()->sync($allPermissionIds);
            } else {
                $permissionIds = isset($matrix[$role->id]) && is_array($matrix[$role->id])
                    ? array_map('intval', array_keys($matrix[$role->id]))
                    : [];

                $role->permissions()->sync($permissionIds);
            }
        }

        return back()->with('status', 'Đã lưu cấu hình ma trận phân quyền sử dụng thành công!');
    }
}
