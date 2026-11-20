<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->input('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'token.required' => 'Mã xác thực token không hợp lệ.',
            'email.required' => 'Vui lòng cung cấp email.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if (! $user || ! Password::broker()->tokenExists($user, $request->input('token'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Mã liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        Password::broker()->deleteToken($user);

        Auth::login($user);

        return redirect()
            ->route('account.show')
            ->with('status', 'Mật khẩu của bạn đã được thay đổi thành công! Bạn đã được đăng nhập tự động.');
    }
}
