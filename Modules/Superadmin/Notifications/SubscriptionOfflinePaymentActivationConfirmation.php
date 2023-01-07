<?php

namespace Modules\Superadmin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionOfflinePaymentActivationConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($business, $package)
    {
        $this->business = $business;
        $this->package = $package;
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
        $details = 'Business: ' . $this->business->name . ', Package: ' . $this->package->name . ', Price: ' . $this->package->price;

        return (new MailMessage)
                ->greeting('Hello!')
                ->line('Please confirm Offline Payment for subscription')
                ->line($details)
                ->line('To confirm go to superadmin subscriptions tab and confirm it.');
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
