<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Không tìm thấy tài khoản nào khớp với địa chỉ email này.']);
        }

        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            // Trường hợp môi trường local chưa cấu hình SMTP mail server
        }

        return back()->with([
            'status' => 'Chúng tôi đã tạo liên kết đặt lại mật khẩu cho tài khoản của bạn.',
            'demo_reset_url' => app()->isLocal() ? $resetUrl : null,
        ]);
    }
}
