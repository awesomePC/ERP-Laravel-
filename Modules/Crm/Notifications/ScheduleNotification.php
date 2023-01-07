<?php

namespace Modules\Crm\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $schedule;
    public $channels;
    public function __construct($schedule, $channels)
    {
        $this->schedule = $schedule;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isPusherEnabled()) {
            $this->channels[] = 'broadcast';
        }

        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Invitation for schedule')
            ->greeting('Hi')
            ->line('You have been invited for the schedule '. $this->schedule->title. ' starts at ' .$this->schedule->start_datetime)
            ->action('Log in', url('/login'));
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
            'schedule_id' => $this->schedule->id,
            'business_id' => $this->schedule->business_id
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
            'title' => $this->schedule->broadcast_title,
            'body' => $this->schedule->body,
            'link' => $this->schedule->link
        ]);
    }
}
