<?php

namespace Modules\Essentials\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Modules\Essentials\Entities\DocumentShare;

class DocumentShareNotification extends Notification
{
    use Queueable;

    protected $document;
    protected $shared_by;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($document, $shared_by)
    {
        $this->document = $document;
        $this->shared_by = $shared_by;
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
    public function toDatabase($notifiable)
    {
        return $this->notificationData();
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $notifiction_data = DocumentShare::documentShareNotificationData($this->notificationData()); 
        return new BroadcastMessage([
            'title' => $notifiction_data['title'],
            'body' => strip_tags($notifiction_data['msg']),
            'link' => $notifiction_data['link']
        ]);
    }

    private function notificationData()
    {
        return [
            'document_id' => $this->document->id,
            'document_name' => $this->document->name,
            'shared_by_name' => $this->shared_by->user_full_name,
            'shared_by_id' => $this->shared_by->id,
            'document_type' => $this->document->type
        ];
    }
}
