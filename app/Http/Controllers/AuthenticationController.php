<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticationController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function storeLogin(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);
        $credentials['status'] = 1;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->safe()->only(['email']))
                ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        $request->session()->regenerate();

        $destination = $request->user()->role()->where('name', 'ADMIN')->exists()
            ? route('admin.dashboard')
            : route('account.show');

        return redirect()->intended($destination);
    }

    public function createRegistration(): View
    {
        return view('auth.register');
    }

    public function storeRegistration(RegisterRequest $request): RedirectResponse
    {
        $customerRole = Role::query()->where('name', 'CUSTOMER')->first();

        if ($customerRole === null) {
            return back()
                ->withInput($request->safe()->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'Chức năng đăng ký chưa được cấu hình vai trò khách hàng.']);
        }

        $user = User::query()->create([
            ...$request->safe()->only(['full_name', 'email', 'phone']),
            'role_id' => $customerRole->id,
            'password' => $request->validated('password'),
            'status' => 1,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('account.show'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Bạn đã đăng xuất.');
    }
}
