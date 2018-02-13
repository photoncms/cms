<?php

namespace Photon\PhotonCms\Dependencies\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Channels\FCM\FCMChannel;
use Photon\PhotonCms\Core\Channels\FCM\FCMNotificationInterface;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class UserUnsubscribed extends Notification implements ShouldQueue
{
    use Queueable;

    private $unsubscribedUser = null;

    private $tableName = '';

    private $entry = null;

    /**
     * Title of the notification.
     * Used for FCM.
     *
     * @var string
     */
    public $title = '';

    /**
     * Body contents of the notification.
     * Used for FCM.
     *
     * @var string
     */
    public $body = '';

    /**
     * String name of a sound set for the notification.
     * Used for FCM.
     *
     * @var string
     */
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
        $unsubscribedUser, 
        $tableName, 
        $entry,
        $title = null,
        $body = '',
        $sound = 'default',
        $icon = 'icon_system_notification',
        $clickAction = 'OPEN_NOTIFICATIONS')
    {
        $this->unsubscribedUser = $unsubscribedUser;        
        $this->tableName = $tableName;
        $this->entry = $entry;

        $this->title = ($title)
            ? $title
            : 'User usubscribed';

        $this->body = ($body)
            ? $body
            : '{$this->unsubscribedUser->anchor_text} has finished editing {$this->entry->anchor_text} entry from {$this->tableName}.';

        $this->sound = $sound;
        $this->icon = $icon;
        $this->clickAction = $clickAction;
    }

    /**
     * Retrieves a notification title.
     * Used for FCM.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Retrieves a notification body.
     * Used for FCM.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Retrieves a notification sound name.
     * Used for FCM.
     *
     * @return string
     */
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
    public function via($operator)
    {
        return [
            'database',
            'broadcast',
            // FCMChannel::class
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
            'operatorId' => $operator->id,
            'unsubscribedUserId' => $this->unsubscribedUser->id,
            'tableName' => $this->tableName,
            'entryId' => $this->entry->id,
            'subject' => trans('emails.new_unsubscriber_subject'),
            'compiled_message' => str_replace(
                '{name}',
                $this->unsubscribedUser->anchor_text,
                str_replace(
                    '{entry_name}',
                    $this->entry->anchor_text,
                    str_replace(
                        '{table_name}',
                        $this->tableName,
                        trans('emails.new_unsubscriber_message')
                    )
                )
            )
        ];
    }
}
