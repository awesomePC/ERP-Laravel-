<?php

namespace Modules\Essentials\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveStatusNotification extends Notification
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
            'ref_no' => $this->leave->ref_no,
            'status' => $this->leave->status,
            'changed_by' => $this->leave->changed_by
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
            'title' => __('essentials::lang.leave_status_changed'),
            'body' => strip_tags( __('essentials::lang.status_change_notification', ['status' => $this->leave->status, 'ref_no' => $this->leave->ref_no, 'admin' => $this->leave->changed_by_user->user_full_name]) ),
            'link' => action('\Modules\Essentials\Http\Controllers\EssentialsLeaveController@index')
        ]);
    }
}
