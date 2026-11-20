<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CouponAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Coupon $coupon)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'coupon_id' => $this->coupon->id,
            'code' => $this->coupon->code,
            'discount_type' => $this->coupon->discount_type,
            'discount_value' => (float) $this->coupon->discount_value,
            'message' => 'Bạn nhận được mã khuyến mãi '.$this->coupon->code.'.',
            'url' => route('checkout.create'),
        ];
    }
}
