<?php

namespace Modules\Superadmin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SendSubscriptionExpiryAlert extends Notification
{
    use Queueable;

    protected $subscription;
    protected $days_left;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
        $this->days_left = \Carbon::now()->diffInDays($this->subscription->end_date) + 1;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['mail', 'database'];
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
        $owner_name = $notifiable->user_full_name;
        $app_name = config('app.name');
        $business_name = $this->subscription->business->name;
        $days_left = $this->days_left;
        return (new MailMessage)
                ->greeting("Dear $owner_name,")
                ->subject('Subscription Expiry Alert')
                ->line("Your subscription for $app_name is expiring in next $days_left days.")
                ->line("Kindly subscribe to continue using $business_name.")
                ->action('Subscribe', action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'days_left' => $this->days_left
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
            'title' => '',
            'body' => strip_tags( __('superadmin::lang.subscription_expiry_alert', ['days_left' => $this->days_left, 'app_name' => config('app.name')]) ),
            'link' => action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')
        ]);
    }
}
