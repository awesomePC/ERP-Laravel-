<?php

namespace Modules\Superadmin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBusinessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($business)
    {
        $this->business = $business;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $details = 'Business: ' . $this->business->name . ', Business Owner: ' . $this->business->owner->user_full_name . ', Email: ' . $this->business->owner->email .
        ', Business contact number: ' . $this->business->locations->first()->mobile;

        return (new MailMessage)
                ->subject('New Business Registration')
                ->greeting('Hello!')
                ->line('New business registered successfully')
                ->line($details);
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
            //
        ];
    }
}
