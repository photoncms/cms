<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Photon\PhotonCms\Core\Channels\FCM\FCMChannel;
use Photon\PhotonCms\Core\Channels\FCM\FCMNotificationInterface;


/**
 * FOR TESTING ONLY!!
 * DO NOT USE THIS CLASS AS AN EXAMPLE!!
 * DO NOT LOOK AT THIS CODE!!
 *
 *
░░░░░░░░░░░░░▄▄▄█▀▀▀▀▀▀▀█▄▄░░░░░░░░░░░░
░░░░░░░░▄▄█▀▀░░░░░░░░░░░░░░▀▀▄░░░░░░░░░
░░░░░░▄█▀░░░░░░░▄▄▄▄░▀░░░░░░░░▀▄░░░░░░░
░░░░░██░░░░░░▀▀▀▀░░░░░░░░░░░░░░░▀▄░░░░░
░░░▄███▄▄░░░░░▀▀▀░░░░░░░░░░░░░░░░░█▄░░░
░░██▀▀░▄░░░░░░░░░░░░░░░░░░░░░░░░░░░▀▄░░
░▄█▀░░░░░░░░░░░░░░░░░░░░░░▄░▄░░░░░░░█▄░
▄█▀▄░░░░░▄█░░░░░░░░░░░░░░███░░░░░░░░░█░
███░░░░░░▀█░░░░░░░░░░░░░▄█░▀▄░░░░░░░░▀▄
██▀██░░▄░░█░░▄▄▄▄▄▄████▀▀░░░░░░░░░░░░░█
██▄▀█▄█████░░█████▀░░▀█░░░░░░░░░░░░░░░█
███░▀▀████░░██▀██▄░▀░▄▄▄▀▀▀░░░░░░░░░░█░
▀███▄░░███░░░▀████████▀░░░░░░░░░░░░░░█░
░▀████▄█▀█░░█▄█████▄░░░░░░░░░░░░░░░░█░░
░░██████▀█▄█▀░▄▄░▀▄▀▄░▄▄█▀░░░░░░░░░█▀░░
░░░▀█████░▀█░░░░░░█▄▀░▀░░░░░░░░░░░█▀░░░
░░░░░▀██▄█▄░▄░░░▄░░▀░░▀░░░░░░░░░▄█░░░░░
░░░░░░░▀█▄▀░█░░░░█▄█░░░░░░░░░░▄█▀█░░░░░
░░░░░░░░░▀▀██░░░░░░▀░░░░░░▄▄▀▀░░░█░░░░░
 *
 *
 *
 *
 */
class FCMTestNotification extends Notification implements ShouldQueue, FCMNotificationInterface
{
    use Queueable;

    public $title = '';

    public $body = '';

    public $sound = '';

    /**
     * Icon name for the notification.
     * Used for FCM.
     *
     * @var string
     */
    public $icon = '';

    /**
     * Click action name for the notification.
     * Used for FCM.
     *
     * @var string
     */
    public $clickAction = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        $title = null,
        $body = '',
        $sound = 'default',
        $icon = 'icon_system_notification',
        $clickAction = 'OPEN_NOTIFICATIONS'
    )
    {
        $this->title = ($title)
            ? $title
            : 'FCM test notification';

        $this->body = $body;
        $this->sound = $sound;
        $this->icon = $icon;
        $this->clickAction = $clickAction;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Retrieves a notification icon name.
     * Used for FCM.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Retrieves a notification click action name.
     * Used for FCM.
     *
     * @return string
     */
    public function getClickAction()
    {
        return $this->clickAction;
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
            'database',
            FCMChannel::class
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($operator)
    {
        return [
            'test' => 'Hello world'
        ];
    }
}
