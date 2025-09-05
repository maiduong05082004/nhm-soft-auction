<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MembershipUser;

class MembershipExpiredNotice extends Notification implements ShouldQueue
{
    use Queueable;

    protected MembershipUser $membershipUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(MembershipUser $membershipUser)
    {
        $this->membershipUser = $membershipUser;
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
            ->subject("Gói membership của bạn đã hết hạn")
            ->greeting("Xin chào " . ($notifiable->name ?? 'Bạn'))
            ->line("Gói {$planName} của bạn đã hết hạn vào ngày {$endDate}.")
            ->line('Để tiếp tục sử dụng các quyền lợi, vui lòng gia hạn hoặc chọn gói khác.')
            ->action('Gia hạn gói', url(route('membership.plans', [], false)))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ.');
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
            'message' => "Gói thành viên đã hết hạn vào ngày {$this->membershipUser->end_date?->format('d/m/Y')}",
        ];
    }
}
