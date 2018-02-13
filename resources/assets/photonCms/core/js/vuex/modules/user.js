import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type {object}
 */
const state = {
    // Stores a validation error
    error: {
        fields: null,
        message: null,
    },
    impersonating: false,
    licenseExpiring: false,
    loggedIn: false,
    meta: { },
};

/**
 * Define the module mutations
 *
 * @type {object}
 */
const mutations = {
    /**
     * Performs a series of state changes after the user has logged in
     *
     * @param  {object}  state
     * @param  {object}  user  User meta data
     * @param  {bool}  impersonating
     * @return  void
      */
    [types.USER_LOGIN_SUCCESS](state, { user, impersonating }) {
        state.loggedIn = true;

        state.meta = user;

        state.impersonating = impersonating;
    },

    /**
     * Handles the automatic mutation commit
     *
     * @return  void
      */
    [types.USER_REGISTER_SUCCESS]() {
        return;
    },

    /**
     * Updates the error message and fields a message relates to
     *
     * @param  {object}  state
     * @param  {object}  user  User meta data
     * @param  {bool}  impersonating
     * @return  void
     */
    [types.AUTH_ERROR_DISPATCH](state, { errorMessage, apiResponse }) {
        state.error.message = errorMessage;

        if (typeof (apiResponse) === 'object' && apiResponse.hasOwnProperty('error_fields')) {
            state.error.fields = apiResponse.error_fields;
        }
    },

    /**
     * Clears the errors from the UI
     *
     * @param  {object}  state
     * @return  void
     */
    [types.AUTH_CLEAR_ERRORS](state) {
        state.error.message = null;

        state.error.fields = null;
    },

    /**
     * Sets the licenseExpiring state property
     *
     * @param  {object}  state
     * @param  {boolean}  status
     * @return  void
     */
    [types.SET_LICENSE_EXPIRING](state, { status }) {
        state.licenseExpiring = status;
    },

    /**
     * Performs a series of actions after the user has logged out
     *
     * @param  {object}  state
     * @return  void
     */
    [types.AUTH_LOGOUT](state) {
        state.loggedIn = false;

        state.meta = { };

        state.impersonating = null;

        state.error.message = null;

        state.error.fields = null;
    },

    /**
     * Set user impersonation to true
     *
     * @param  {object}  state
     * @return  void
     */
    [types.AUTH_IMPERSONATING_ON](state) {
        state.impersonating = true;
    },

    /**
     * Set user impersonation to false
     *
     * @param  {object}  state
     * @return  void
     */
    [types.AUTH_IMPERSONATING_OFF](state) {
        state.impersonating = false;
    },

    /**
     * Sets the error state properties
     */
    [types.IMPERSONATE_ERROR_DISPATCH](state, { errorMessage }) {
        state.error.message = errorMessage;
    },
};

/**
 * Define the module getters
 *
 * @type {object}
 */
const getters = {
    user: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type {object}
 */
import actions from '_/vuex/actions/userActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state,
};
