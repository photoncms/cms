import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    allNotifications: [],
    notifications: [],
    notificationsBadge: 0,
    unreadNotifications: []
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Adds new notifications to the existing stack
     *
     * @param  {object}  state
     * @param  {object}  notification  Object used to instantiate and run pNotify notification
     * @return  {void}
     */
    [types.NOTIFICATIONS_ADD](state, notification) {
        state.notifications.push(notification);
    },

    /**
     * Clears the contents of a notifications array
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.NOTIFICATIONS_EMPTY](state) {
        state.notifications = [];
    },

    /**
     * Sets the number of unread notifications
     *
     * @param  {object}  state
     * @param  {int}  value  Number of unread notifications
     * @return  {void}
     */
    [types.NOTIFICATIONS_UPDATE_BADGE](state, value) {
        state.notificationsBadge = value;
    },

    /**
     * Populates the unreadNotifications store with unread notifications objects
     *
     * @param  {object}  state
     * @param  {array}  value  An array of notifications objects
     * @return  {void}
     */
    [types.NOTIFICATIONS_UPDATE_UNREAD](state, value) {
        state.unreadNotifications = value;
    },

    /**
     * Populates the allNotifications store with all notifications objects
     *
     * @param  {object}  state
     * @param  {array}  value  An array of notifications objects
     * @return  {void}
     */
    [types.NOTIFICATIONS_READ](state, value) {
        state.allNotifications = value;
    }
};

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    notification: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/notificationActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
