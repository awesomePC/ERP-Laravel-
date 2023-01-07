<?php

namespace Modules\AssetManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssetSentForMaintenance extends Notification
{
    use Queueable;

    protected $notificationInfo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notificationInfo)
    {
       $this->notificationInfo = $notificationInfo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->notificationInfo['via'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = $this->notificationInfo;

        $mail = (new MailMessage)
                    ->subject($data['subject'])
                    ->view(
                        'emails.plain_html',
                        ['content' => $data['body']]
                    );

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'msg' => $this->notificationInfo['subject']
        ];
    }
}
