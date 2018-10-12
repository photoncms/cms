<?php

namespace Photon\PhotonCms\Core\Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Photon\PhotonCms\Core\Channels\FCM\FCMChannel;
use Photon\PhotonCms\Core\Channels\FCM\FCMNotificationInterface;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class CelebritiesTagged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Tagged asset entry
     *
     * @var  Asset
     */
    private $entry;

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
     * @param  Eloquent  $entry
     * @param  Array  $recognizedFaces
     * @param  string  $sound
     * @param  string  $icon
     * @param  string  $clickAction
     */
    public function __construct(
        $entry,
        $recognizedFaces,
        $sound = 'default',
        $icon = 'icon_system_notification',
        $clickAction = 'OPEN_NOTIFICATIONS')
    {
        $this->entry = $entry;

        $this->title = trans('emails.face_recognition_completed');

        $tagsString = $this->tagsToString($recognizedFaces);

        $this->body = trans('emails.file_has_been_processed', [ 'file_name' => $this->entry->file_name ]) . $tagsString;

        $this->sound = $sound;

        $this->icon = $icon;

        $this->clickAction = $clickAction;
    }

    /**
     * Creates a tags string by concatinating all the tag titles to one sentence.
     *
     * @param   Array  $recognizedFaces
     * @return  String
     */
    private function tagsToString($recognizedFaces)
    {
        if(empty($recognizedFaces)) {
            return trans('emails.no_celebrities_were_recognized');
        }

        $text = trans('emails.tagged_celebrities');

        $text .= implode(', ', $recognizedFaces);

        return $text;
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
            'entry_id' => $this->entry->id,
            'subject' => $this->title,
            'compiled_message' => $this->body,
        ];
    }
}
