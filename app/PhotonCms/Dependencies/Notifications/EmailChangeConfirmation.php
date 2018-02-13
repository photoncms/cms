<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class EmailChangeConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    private $confirmationCode = '';

    private $newEmail = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newEmail, $confirmationCode)
    {
        $this->newEmail = $newEmail;
        $this->confirmationCode = $confirmationCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($item)
    {
        // Prepare confirmation URL for the email
        $confirmationUrl = RoutesHelper::getEmailChangeConfirmationUrl($item->user_id, $this->confirmationCode);

        return (new MailMessage)
            ->from(config('photon.service_emails.registration'))
            ->subject(trans('emails.email_change_request_title'))
            ->greeting(trans('emails.email_change_request_greeting'))
            ->line(trans('emails.email_change_request_intro'))
            ->action(trans('emails.email_change_request_action'), $confirmationUrl)
            ->line(trans('emails.email_change_request_outro'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
