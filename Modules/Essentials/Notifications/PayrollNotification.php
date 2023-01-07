<?php

namespace Modules\Essentials\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PayrollNotification extends Notification
{
    use Queueable;

    protected $payroll;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payroll)
    {
        $this->payroll = $payroll;
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
        $transaction_date = \Carbon::parse($this->payroll->transaction_date);
        return [
            "month" => $transaction_date->format('m'),
            "year" => $transaction_date->format('Y'),
            "ref_no" => $this->payroll->ref_no,
            "action" => $this->payroll->action,
            'created_by' => $this->payroll->created_by
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
        $msg = '';
        $title = '';
        $transaction_date = \Carbon::parse($this->payroll->transaction_date);
        $month = \Carbon::createFromFormat('m', $transaction_date->format('m'))->format('F');
        if ($this->payroll->action == 'created') {
            $msg = __('essentials::lang.payroll_added_notification', ['month_year' => $month . '/' . $transaction_date->format('Y') , 'ref_no' => $this->payroll->ref_no , 'created_by' => $this->payroll->sales_person->user_full_name]);
            $title = __('essentials::lang.payroll_added');
        } elseif ($this->payroll->action == 'updated') {
            $msg = __('essentials::lang.payroll_updated_notification', ['month_year' => $month . '/' . $transaction_date->format('Y'), 'ref_no' => $this->payroll->ref_no, 'created_by' => $this->payroll->sales_person->user_full_name]);
            $title = __('essentials::lang.payroll_updated');
        }

        return new BroadcastMessage([
            'title' => $title,
            'body' => $msg,
            'link' => action('\Modules\Essentials\Http\Controllers\PayrollController@index')
        ]);
    }
}
