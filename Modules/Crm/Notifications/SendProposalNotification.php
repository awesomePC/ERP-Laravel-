<?php

namespace Modules\Crm\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendProposalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $proposal;
    public $media;
    public function __construct($proposal)
    {
        $this->proposal = $proposal;
        $this->media = $proposal->media;
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
        $mail = (new MailMessage)
                ->subject($this->proposal->subject)
                ->view(
                    'emails.plain_html',
                    ['content' => $this->proposal->body]
                );
                
        if ($this->media->count() > 0) {
            foreach ($this->media as $media) {
                $mail->attach(public_path('uploads').'/media/'.$media->file_name, ['as' => $media->display_name]);
            }
        }
        return $mail;
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
