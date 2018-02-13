import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    errorCode: 404,
    errorIcon: 'fa fa-frown',
    errorText: 'Page Not Found'
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Update the error module state
     *
     * @param  {object}  state
     * @param  {string}  errorCode
     * @param  {string}  errorIcon
     * @param  {string}  errorText
     * @return  {void}
     */
    [types.UPDATE_ERROR_PAGE](state, {errorCode, errorIcon, errorText}) {
        state.errorCode = errorCode;

        state.errorIcon = errorIcon;

        state.errorText = errorText;
    }
};


/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    error: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/errorActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
