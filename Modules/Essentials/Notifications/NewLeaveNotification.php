<?php

namespace Modules\Essentials\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewLeaveNotification extends Notification
{
    use Queueable;

    protected $leave;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        if (isPusherEnabled()) {
            $channels[] = 'broadcast';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "applied_by" => $this->leave->user_id,
            "ref_no" => $this->leave->ref_no,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => __('essentials::lang.leave_added_successfully'),
            'body' => strip_tags( __('essentials::lang.new_leave_notification', ['employee' => $this->leave->user->user_full_name, 'ref_no' => $this->leave->ref_no]) ),
            'link' => action('\Modules\Essentials\Http\Controllers\EssentialsLeaveController@index')
        ]);
    }
}
