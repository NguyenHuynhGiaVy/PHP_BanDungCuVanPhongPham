<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ mới từ khách hàng</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #2563eb; color: #ffffff; padding: 15px 20px; border-radius: 6px 6px 0 0;">
        <h2 style="margin: 0; font-size: 20px;">Việt Thịnh - Tin nhắn liên hệ mới</h2>
    </div>
    <div style="border: 1px solid #e5e7eb; border-top: none; padding: 20px; border-radius: 0 0 6px 6px; background-color: #ffffff;">
        <p>Bạn vừa nhận được một yêu cầu liên hệ / hỗ trợ mới từ website:</p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 140px; color: #4b5563;">Họ và tên:</td>
                <td style="padding: 8px 0;">{{ $contact->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Số điện thoại:</td>
                <td style="padding: 8px 0;">{{ $contact->phone }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Email:</td>
                <td style="padding: 8px 0;"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Chủ đề:</td>
                <td style="padding: 8px 0;">{{ $contact->subject }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Thời gian:</td>
                <td style="padding: 8px 0;">{{ $contact->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>

        <div style="background-color: #f9fafb; border-left: 4px solid #2563eb; padding: 15px; margin-top: 15px;">
            <strong style="display: block; margin-bottom: 8px; color: #1f2937;">Nội dung tin nhắn:</strong>
            <p style="margin: 0; white-space: pre-wrap;">{{ $contact->message }}</p>
        </div>

        <p style="margin-top: 25px; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            Email này được gửi tự động từ hệ thống Văn phòng phẩm Việt Thịnh.
        </p>
    </div>
</body>
</html>

