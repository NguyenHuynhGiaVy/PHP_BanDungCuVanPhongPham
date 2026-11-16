<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('account.show', [
            'user' => $user,
            'recentOrders' => $user->orders()->latest()->limit(5)->get(),
            'addresses' => $user->addresses()->latest()->get(),
        ]);
    }

    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('status', 'Thông tin tài khoản đã được cập nhật.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('status', 'Mật khẩu đã được cập nhật.');
    }
}
