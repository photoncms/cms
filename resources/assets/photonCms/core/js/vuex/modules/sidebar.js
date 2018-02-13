import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    sidebarType: null // Type of sidebar (which sidebar gets loaded depends on this value)
};

/**
 * Define the module mutations
 *
 * @type {object}
 */
const mutations = {
    /**
     * Updates a sidebarType state property
     *
     * @param  {string}  sidebarType
     * @return  {void}
     */
    [types.UI_UPDATE_SIDEBAR_TYPE](state, sidebarType) {
        state.sidebarType = sidebarType;
    }
};

/**
 * Define the module getters
 *
 * @type {object}
 */
const getters = {
    sidebar: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type {object}
 */
import actions from '_/vuex/actions/sidebarActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
