<?php

namespace Photon\PhotonCms\Core\Channels\FCM;

interface FCMNotificationInterface
{

    /**
     * Retrieves a notification title.
     * Used for FCM.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Retrieves a notification body.
     * Used for FCM.
     *
     * @return string
     */
    public function getBody();

    /**
     * Retrieves a notification sound name.
     * Used for FCM.
     *
     * @return string
     */
    public function getSound();

    /**
     * Retrieves a notification icon name.
     * Used for FCM.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Retrieves a notification click action name.
     * Used for FCM.
     *
     * @return string
     */
    public function getClickAction();
}