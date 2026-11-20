<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'subject.required' => 'Vui lòng nhập chủ đề cần hỗ trợ.',
            'message.required' => 'Vui lòng nhập nội dung chi tiết.',
        ]);

        $contact = Contact::create($validated);

        try {
            $adminEmail = config('mail.from.address') ?: 'hotro@vietthinh.vn';
            Mail::to($adminEmail)->send(new ContactMessageReceived($contact));
        } catch (Throwable $e) {
            Log::error('Lỗi gửi email liên hệ: '.$e->getMessage());
        }

        return back()->with('status', 'Cảm ơn bạn! Chúng tôi đã nhận được yêu cầu hỗ trợ và sẽ phản hồi trong vòng 24 giờ làm việc.');
    }
}
