<?php

namespace Modules\Crm\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCampaignNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $campaign;
    public $business;
    public function __construct($campaign, $business)
    {
        $this->campaign = $campaign;
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
        $subject = preg_replace(["/{contact_name}/", "/{campaign_name}/", "/{business_name}/"], [$notifiable->name, $this->campaign->name, $this->business->name], $this->campaign->subject);

        $body = preg_replace(["/{contact_name}/", "/{campaign_name}/", "/{business_name}/"], [$notifiable->name, $this->campaign->name, $this->business->name], $this->campaign->email_body);

        return (new MailMessage)
            ->subject($subject)
            ->view(
                'emails.plain_html',
                ['content' => $body]
            );
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
