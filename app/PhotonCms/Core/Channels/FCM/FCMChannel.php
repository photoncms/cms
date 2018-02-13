<?php

namespace Photon\PhotonCms\Core\Channels\FCM;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Photon\PhotonCms\Core\Channels\FCM\FCMNotificationInterface;
use Photon\PhotonCms\Core\Channels\FCM\FCMTokenCache;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class FCMChannel
{

    /**
     * Send the notification to FCM.
     *
     * @param mixed $notifiable
     * @param FCMNotificationInterface $notification
     * @return void
     */
    public function send($notifiable, FCMNotificationInterface $notification)
    {
        $data = (method_exists($notification, 'toFCM'))
            ? $notification->toFCM($notifiable)
            : $notification->toArray($notifiable);

        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($notification->getTitle());
        $notificationBuilder->setBody($notification->getBody())
                            ->setSound($notification->getSound())
                            ->setIcon($notification->getIcon())
                            ->setClickAction($notification->getClickAction());

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuiler->build();
        $compiledNotification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = FCMTokenCache::getUserTokens($notifiable->id);

        foreach ($tokens as $token) {
            $downstreamResponse = FCM::sendTo($token, $option, $compiledNotification, $data);
        }
    }

    /**
     * Check if this is necessary.
     *
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @param $response
     */
    protected function handleFailedNotifications($notifiable, Notification $notification, $response)
    {
        $results = $response->getResults();

        foreach ($results as $token => $result) {
            if (! isset($result['error'])) {
                continue;
            }

            $this->events->fire(
                new NotificationFailed($notifiable, $notification, $this, [
                    'token' => $token,
                    'error' => $result['error'],
                ])
            );
        }
    }
}