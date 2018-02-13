import * as types from '_/vuex/mutation-types';

import { api } from '_/services/api';

import { errorCommit } from '_/vuex/actions/commonActions';

import { router } from '_/router/router';

import { pError } from '_/helpers/logger';

/**
 * Removes notifications from the storage
 *
 * @return  {void}
 */
const _emptyNotifications = ({commit}) => {
    commit(types.NOTIFICATIONS_EMPTY);
};

/**
 * TODO: This function is a mapper that shouldn't generate actionable URLs. Actionable URLs need to be set on the API side instead.
 *
 * @param  {object}  notification
 */
const _navigateToActionableItem = (notification) => {
    switch (notification.type) {
    case 'NewUserRegistered':
        router.push('/admin/users/' + notification.user_id);
        break;
    default:
        pError('No actionable item defined (notifiable_id = ' + notification.notifiable_id + ') for notification ' + notification.id);
    }
};

export default {

    /**
     * Adds new notifications to the state
     *
     * @param  {function}  options.commit
     * @param  {array}  notifications  Accepts single or many notification objects
     * @return  {void}
     */
    addNotifications ({commit}, { notifications }) {
        if (notifications.length > 0) {
            notifications.forEach(function(notification) {

                const pNotification = {
                    title: notification.subject,
                    text: notification.compiled_message,
                    history: false,
                    type: 'info',
                    nonblock: true,
                    nonblock_opacity: .25
                };

                commit(types.NOTIFICATIONS_ADD, pNotification);

            });
        }
    },

    /**
     * Shows notifications
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @return  {void}
     */
    showNotifications ({commit, state}) {
        return new Promise(function(resolve, reject) {
            if (state.notifications.length) {
                const firedNotifications = [];

                state.notifications.forEach(function(val) {
                    firedNotifications.push(val);

                    $.pnotify(val);
                });

                _emptyNotifications({ commit });

                resolve(firedNotifications);
            }

            reject('Notifications array was empty.');
        });
    },

    /**
     * Commits notifications badge number
     *
     * @param  {function}  options.commit
     * @return  {void}
     */
    updateNotificationsBadge ({commit}) {
        commit(types.NOTIFICATIONS_UPDATE_BADGE);
    },

    /**
     * Gets the unread notifications list. Used to refresh the notifications list
     * after being pinged by a notification service such as Pusher
     *
     * @param  {function}  options.commit
     * @param  {boolean}  options.notify  Fires a notification if true
     * @return {promise}
     */
    getUnreadNotifications ({dispatch, commit}, { notify = false } = {}) {
        return api.post('notifications/unread')
            .then((response) => {
                commit(types.NOTIFICATIONS_UPDATE_UNREAD, response.data.body.notifications);

                commit(types.NOTIFICATIONS_UPDATE_BADGE, response.data.body.notifications.length);

                // Fire only the last notification using pNotify
                if (response.data.body.notifications.length > 0 && notify) {
                    return dispatch('addNotifications', {
                        notifications: [response.data.body.notifications[0]]
                    });
                }
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'NOTIFICATIONS');
            });
    },

    /**
     * Gets all notfications from the API
     *
     * @param  {function}  options.commit
     * @return {promise}
     */
    getAllNotifications ({commit}) {
        return api.post('notifications/all')
            .then((response) => {
                commit(types.NOTIFICATIONS_READ, response.data.body.notifications);
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'NOTIFICATIONS');
            });
    },

    /**
     * Marks all notifications as read and fetches an updated list of notifications on sucess
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @return {promise}
     */
    markAllNotificationsAsRead ({dispatch, commit, state}) {
        return dispatch('getAllNotifications', { commit } ).
            then(() => {
                let promiseLoad = [];

                state.allNotifications.forEach(function(notification) {
                    if (!notification.read_at) { // If a notification is unread
                        promiseLoad.push(api.get('notifications/read/' + notification.id)); // Stack a promise in a promise load
                    }
                });

                Promise.all(promiseLoad)
                    .then(() => {
                        dispatch('getUnreadNotifications', { commit, dispatch });

                        return dispatch('getAllNotifications', { commit } );
                    })
                    .catch((response) => {
                        errorCommit({ commit }, response, 'NOTIFICATIONS');
                    });
            });

    },

    /**
     * Marks a single notification as read and fetches an updated list of notifications on sucess
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @return {promise}
     */
    readNotification ({dispatch, commit, state} , notification) {
        if (notification.read_at) {
            _navigateToActionableItem(notification);
        }

        api.get('notifications/read/' + notification.id)
            .then(() => {
                _navigateToActionableItem(notification);

                return dispatch('getAllNotifications', { commit } ); // Fetch all notifications again
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'NOTIFICATIONS');
            });
    }
};

