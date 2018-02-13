import _ from 'lodash';

import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    /**
     * Subscribed module table name
     *
     * @type  {string}
     */
    moduleTableName: null,
    /**
     * Subscribed entry id
     *
     * @type  {integer}
     */
    entryId: null,
    /**
     * Other subscribed users
     *
     * @type  {Array}
     */
    subscribedUsers: [],
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Adds a single subscribed user to subscribedUsers state property
     *
     * @param  {object}  options.state
     * @param  {object}  options.subscribedUser
     * @return  {void}
     */
    [types.ADD_SUBSCRIBED_USER](state, { subscribedUser }) {
        if (_.find(state.subscribedUsers, { id: subscribedUser.id })) {
            return;
        }

        state.subscribedUsers = subscribedUser;
    },

    /**
     * Adds a single subscribed user to subscribedUsers state property
     *
     * @param  {object}  options.state
     * @param  {object}  options.subscribedUser
     * @return  {void}
     */
    [types.REMOVE_SUBSCRIBED_USER](state, { subscribedUser }) {
        const userIndex  = _.findIndex(state.subscribedUsers, { id: subscribedUser.id });
        if (userIndex > -1) {
            state.subscribedUsers.splice(userIndex, 1);
        }
    },

    /**
     * Set the subscribed users
     *
     * @param  {object}  options.state
     * @param  {array}  options.subscribedUsers
     * @return  {void}
     */
    [types.SET_SUBSCRIBED_USERS](state, { subscribedUsers }) {
        state.subscribedUsers = subscribedUsers;
    },

    /**
     * Set entryId and moduleTableName state parameters upon successfull subscription
     *
     * @param  {object}  options.state
     * @param  {object}  options.entryId
     * @param  {object}  options.moduleTableName
     * @return  {void}
     */
    [types.SUBSCRIBE](state, { entryId, moduleTableName }) {
        state.entryId = entryId;

        state.moduleTableName = moduleTableName;
    },

    /**
     * Unset the subscribed users
     *
     * @param  {object}  options.state
     * @return  {void}
     */
    [types.UNSET_SUBSCRIBED_USERS](state) {
        state.subscribedUsers = {};
    },

    /**
     * Clear the entryId and moduleTableName subscription data
     *
     * @param  {object}  options.state
     * @return  {void}
     */
    [types.UNSUBSCRIBE](state) {
        state.entryId = null;

        state.moduleTableName = null;
    },
};


/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    subscription: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/subscriptionActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
