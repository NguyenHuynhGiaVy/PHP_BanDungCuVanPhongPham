<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $staffUsers = User::query()
            ->whereHas('role', static fn ($q) => $q->whereIn('name', ['ADMIN', 'STAFF']))
            ->with('role')
            ->when($search, static fn ($query, $term) => $query
                ->where('full_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%"))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.staff.index', [
            'staffUsers' => $staffUsers,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $roles = Role::whereIn('name', ['ADMIN', 'STAFF'])->get();

        return view('admin.staff.form', [
            'staff' => new User,
            'roles' => $roles,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:0,1'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 8 ký tự.',
        ]);

        User::create([
            'role_id' => $validated['role_id'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => (int) $validated['status'],
        ]);

        return redirect()->route('admin.staff.index')
            ->with('status', 'Thêm mới nhân viên thành công!');
    }

    public function edit(User $staff): View
    {
        $roles = Role::whereIn('name', ['ADMIN', 'STAFF'])->get();

        return view('admin.staff.form', [
            'staff' => $staff,
            'roles' => $roles,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'in:0,1'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.min' => 'Mật khẩu phải có tối thiểu 8 ký tự.',
        ]);

        $updateData = [
            'role_id' => $validated['role_id'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => (int) $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $staff->update($updateData);

        return redirect()->route('admin.staff.index')
            ->with('status', 'Cập nhật thông tin nhân viên thành công!');
    }

    public function destroy(User $staff): RedirectResponse
    {
        if ($staff->id === auth()->id()) {
            return back()->withErrors(['staff' => 'Bạn không thể tự xóa tài khoản của chính mình.']);
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('status', 'Đã xóa tài khoản nhân viên thành công.');
    }
}
