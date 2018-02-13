<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class RegistrationSuccess extends Notification implements ShouldQueue
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
        $applicationUrl = RoutesHelper::getApplicationUrl();

        return (new MailMessage)
            ->from(config('photon.service_emails.registration'))
            ->subject(trans('emails.registration_success_title'))
            ->greeting(
                str_replace(
                    '{last_name}',
                    $user->last_name,
                    str_replace(
                        '{first_name}',
                        $user->first_name,
                        trans('emails.registration_success_greeting')
                    )
                )
            )
            ->line(trans('emails.registration_success_intro_1'))
            ->line(trans('emails.registration_success_intro_2'))
            ->action(trans('emails.registration_success_action'), $applicationUrl)
            ->line(trans('emails.registration_success_outro'));
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
