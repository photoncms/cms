<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class NewUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public $newUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newUser)
    {
        $this->newUser = $newUser;
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
            'mail',
            'broadcast',
            'database'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($invitation)
    {
        $newUserReviewUrl = RoutesHelper::getAbsoluteNewUserReviewUrl($this->newUser);
        
        return (new MailMessage)
            ->from(config('photon.service_emails.registration'))
            ->subject(
                str_replace(
                    '{last_name}',
                    $this->newUser->last_name,
                    str_replace(
                        '{first_name}',
                        $this->newUser->first_name,
                        trans('emails.new_user_registered_title')
                    )
                )
            )
            ->greeting(
                str_replace(
                    '{last_name}',
                    $this->newUser->last_name,
                    str_replace(
                        '{first_name}',
                        $this->newUser->first_name,
                        trans('emails.new_user_registered_greeting')
                    )
                )
            )
            ->line(trans('emails.new_user_registered_intro'))
            ->action(
                str_replace(
                    '{last_name}',
                    $this->newUser->last_name,
                    str_replace(
                        '{first_name}',
                        $this->newUser->first_name,
                        trans('emails.new_user_registered_action')
                    )
                ),
                $newUserReviewUrl
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $newUserReviewUrl = RoutesHelper::getNewUserReviewUrl($this->newUser);

        return [
            'user_id' => $this->newUser->id,
            'user_review_link' => $newUserReviewUrl,
            'subject' => str_replace(
                '{last_name}',
                $this->newUser->last_name,
                str_replace(
                    '{first_name}',
                    $this->newUser->first_name,
                    trans('emails.new_user_registered_greeting')
                )
            ),
            'compiled_message' => str_replace(
                '{last_name}',
                $this->newUser->last_name,
                str_replace(
                    '{first_name}',
                    $this->newUser->first_name,
                    trans('emails.new_user_registered_title')
                )
            )
        ];
    }
}
