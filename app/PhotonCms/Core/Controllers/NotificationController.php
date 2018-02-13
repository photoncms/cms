<?php

namespace Photon\PhotonCms\Core\Controllers;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\NotificationHelperFactory;
use Photon\PhotonCms\Core\Channels\FCM\FCMTokenCache;

class NotificationController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     */
    public function __construct(
        ResponseRepository $responseRepository
    )
    {
        $this->responseRepository = $responseRepository;
    }

    /**
     * Sends the specified notification.
     *
     * @param string $notificationName
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws PhotonException
     */
    public function notify($notificationName)
    {
        $data = \Request::all();
        
        $notificationHelper = NotificationHelperFactory::makeByHelperName($notificationName);

        if ($notificationHelper) {
            $notificationHelper->notify($data);
            return $this->responseRepository->make('NOTIFICATION_SENT');
        }
        else {
            throw new PhotonException('NOTIFICATION_HELPER_DOESNT_EXIST', ['notification_name' => $notificationName]);
        }
    }

    /**
     * Marks the specific notification as read.
     *
     * @param int $notificationId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws PhotonException
     */
    public function read($notificationId)
    {
        $user = \Auth::user();
        $requestedNotification = null;
        foreach ($user->unreadNotifications as $notification) {
            if ($notification->id == $notificationId) {
                $requestedNotification = $notification;
            }
        }

        if ($requestedNotification) {
            $requestedNotification->markAsRead();
            return $this->responseRepository->make('NOTIFICATION_READ');
        }
        else {
            throw new PhotonException('NOTIFICATION_NOT_FOUND', ['notification_id' => $notificationId]);
        }
    }

    /**
     * Counts unread notifications of the currently logged in user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countUnreadNotifications()
    {
        $user = \Auth::user();

        $count = $user->unreadNotifications()->count();

        return $this->responseRepository->make('GET_UNREAD_NOTIFICATIONS_COUNT_SUCCESS', ['count' => $count]);
    }

    /**
     * Retrieves notifications of the currently logged in user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getNotifications()
    {
        $user = \Auth::user();

        $queryBuilder = $user->notifications();

        $paginationData = \Request::get('pagination');

        if (is_array($paginationData)) {
            $itemsPerPage = (isset($paginationData['items_per_page'])) ? $paginationData['items_per_page'] : 10;
            $currentPage = (isset($paginationData['current_page'])) ? $paginationData['current_page'] : 1;

            \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });

            $paginator = $queryBuilder->paginate($itemsPerPage);

            if ($paginator->currentPage() > $paginator->lastPage()) {
                $lastPage = $paginator->lastPage();
                \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($lastPage) {
                    return $lastPage;
                });
                $paginator = $queryBuilder->paginate($itemsPerPage);
            }
            return $this->responseRepository->make('GET_NOTIFICATIONS_SUCCESS', ['notifications' => $paginator->items(), 'pagination' => $paginator]);
        }
        

        return $this->responseRepository->make('GET_NOTIFICATIONS_SUCCESS', ['notifications' => $queryBuilder->get()]);
    }

    /**
     * Retrieves unread notifications of the currently logged in user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUnreadNotifications()
    {
        $user = \Auth::user();

        $queryBuilder = $user->unreadNotifications();

        $paginationData = \Request::get('pagination');

        if (is_array($paginationData)) {
            $itemsPerPage = (isset($paginationData['items_per_page'])) ? $paginationData['items_per_page'] : 10;
            $currentPage = (isset($paginationData['current_page'])) ? $paginationData['current_page'] : 1;

            \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });

            $paginator = $queryBuilder->paginate($itemsPerPage);

            if ($paginator->currentPage() > $paginator->lastPage()) {
                $lastPage = $paginator->lastPage();
                \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($lastPage) {
                    return $lastPage;
                });
                $paginator = $queryBuilder->paginate($itemsPerPage);
            }
            return $this->responseRepository->make('GET_UNREAD_NOTIFICATIONS_SUCCESS', ['notifications' => $paginator->items(), 'pagination' => $paginator]);
        }


        return $this->responseRepository->make('GET_UNREAD_NOTIFICATIONS_SUCCESS', ['notifications' => $queryBuilder->get()]);
    }

    /**
     * Assigns the specified FCM token to the currently logged in user.
     *
     * @param string $token
     */
    public function assignFCMToken($token)
    {
        $user = \Auth::user();

        FCMTokenCache::addUserToken($user->id, $token);

        return $this->responseRepository->make('FCM_TOKEN_ADD_SUCCESS');
    }

    /**
     * Revokes the specified FCM token from the currently logged in user.
     *
     * @param string $token
     */
    public function revokeFCMToken($token)
    {
        $user = \Auth::user();

        FCMTokenCache::removeUserToken($user->id, $token);

        return $this->responseRepository->make('FCM_TOKEN_REMOVE_SUCCESS');
    }
}