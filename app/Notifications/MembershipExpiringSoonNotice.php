<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MembershipUser;

class MembershipExpiringSoonNotice extends Notification implements ShouldQueue
{
    use Queueable;

    protected MembershipUser $membershipUser;
    protected int $daysBefore;

    public function __construct(MembershipUser $membershipUser, int $daysBefore = 3)
    {
        $this->membershipUser = $membershipUser;
        $this->daysBefore = $daysBefore;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array 
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage 
    {
        $planName = $this->membershipUser->membershipPlan->name ?? 'gói thành viên';
        $endDate = $this->membershipUser->end_date ? $this->membershipUser->end_date->format('d/m/Y') : null;

        return (new MailMessage)
            ->subject("Nhắc nhở: Gói {$planName} của bạn sắp hết hạn")
            ->line("Gói {$planName} của bạn sẽ hết hạn vào ngày {$endDate}.")
            ->line("Đây là thông báo trước {$this->daysBefore} ngày để bạn kịp thời gia hạn.")
            ->action('Gia hạn ngay', url(route('membership.plans', [], false)))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.');
    }

    /**
     * Store data for database notifications.
     */
    public function toArray(object $notifiable): array 
    {
        return [
            'membership_user_id' => $this->membershipUser->id,
            'membership_plan_id' => $this->membershipUser->membership_plan_id,
            'plan_name' => $this->membershipUser->membershipPlan->name ?? null,
            'end_date' => $this->membershipUser->end_date?->toDateString(),
            'days_before' => $this->daysBefore,
            'message' => "Gói thành viên sẽ hết hạn vào ngày {$this->membershipUser->end_date?->format('d/m/Y')} (còn {$this->daysBefore} ngày)", // ✅ Cải thiện message
        ];
    }
}