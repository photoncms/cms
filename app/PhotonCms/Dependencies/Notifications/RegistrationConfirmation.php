<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class RegistrationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
    public function toMail($user)
    {
        // Prepare confirmation URL for the email
        $confirmationUrl = RoutesHelper::getAuthConfirmationUrl($user->confirmation_code);

        return (new MailMessage)
            ->from(config('photon.service_emails.registration'))
            ->subject(trans('emails.registration_confirmation_title'))
            ->greeting(trans('emails.registration_confirmation_greeting'))
            ->line(trans('emails.registration_confirmation_intro'))
            ->action(trans('emails.registration_confirmation_action'), $confirmationUrl);
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
