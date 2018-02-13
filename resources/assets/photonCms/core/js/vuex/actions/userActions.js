import Vue from 'vue';
import * as types from '_/vuex/mutation-types';
import _ from 'lodash';
import { storage } from '_/services/storage';
import { api } from '_/services/api';
import { router } from '_/router/router';
import { store } from '_/vuex/store';
import { pError } from '_/helpers/logger';
import {
    apiResponseCommit,
    errorCommit,
} from '_/vuex/actions/commonActions';

/**
 * Initializes a refresh interval variable
 *
 * @type  mixed
 */
let _refreshInterval = false;

/**
 * Refresh callback
 *
 * @param  {object}  response  API response
 * @return  {void}
 */
const _refreshCallback = (response) => {
    const apiToken = response.data.body.token.token;

    storage.save('apiToken', apiToken, true);

    Vue.http.headers.common['Authorization'] = 'Bearer ' + apiToken;

    const ttl = response.data.body.token.ttl;

    _startRefreshing(ttl);
};


/**
 * Gets a new token from API and restarts the refresh interval
 *
 * @return {object}  API return
 */
const _getRefreshToken = () => {
    return api.get('auth/refresh')
        // On success call _refreshCallback (with response as an argument)
        .then(_refreshCallback)
        .catch((error) => {
            pError('Token refresh failed.', error);
        });
};

/**
 * Checks if a user has given role
 *
 * @param   {string}  role
 * @return  {bool}
 */
export const userHasRole = (role) => {
    return _.findIndex(store.state.user.meta.roles, { 'name': role }) > -1;
};


/**
 * Starts the refresh interval
 *
 * @param  {int}  ttl  TTL in minutes
 * @return  {void}
 */
const _startRefreshing = (ttl) => {
    const refreshTime = Math.max(
        // Convert to ms after subtracting 1 min
        (ttl - 1) * 60 * 1000,
        // Minimum refresh interval is 5000ms
        5000
    );

    if (_refreshInterval) {
        clearInterval(_refreshInterval);
    }

    _refreshInterval = setInterval(_getRefreshToken, refreshTime);
};

/**
 * Helper function to process login, takes user response and login payload
 *
 * @param  {function}  options.commit
 * @param  {object}  response  API response
 * @param  {string}  redirectPath
 * @return  {void}
 */
const _processLogin = (commit, response, redirectPath) => {
    _refreshCallback(response);

    apiResponseCommit({ commit }, response, 'AUTH');

    router.push(redirectPath);
};

export default {
    /**
     * Checks the license validity
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.force  If set to true, remote API will be called instead of pulling from local API
     * cache, even if the cache TTL is still valid.
     * @return  {promise}
     */
    checkLicense ({ commit }, { force = false } = {}) {
        const payload = force
            ? {
                params: {
                    force: 1
                }
            }
            : {};

        let licenseStatus = force ? null : storage.get('licenseStatus', true);

        // If license status was retrived from local storage
        if (licenseStatus) {
            const licenseStatusTimestamp = moment(licenseStatus.timestamp);

            const currentTime = moment();

            const age = currentTime.diff(licenseStatusTimestamp, 'seconds');

            // Don't fetch the status from API if local storage data is younger then 30 minutes
            if(age < 1800) {
                if(licenseStatus.message === 'PHOTON_LICENSE_KEY_EXPIRING') {
                    commit(types.SET_LICENSE_EXPIRING, { status: true });
                } else {
                    commit(types.SET_LICENSE_EXPIRING, { status: false });
                }

                return licenseStatus.message;
            }

            // Else remove the licenseStatus record from local storage
            storage.remove('licenseStatus');
        }

        storage.remove('licenseStatus');

        return api.get('ping-home', payload)
            .then((response) => {
                const message = response.data.message;
                const body = response.data.body;

                if (message === 'PHOTON_LICENSE_KEY_GENERATED'
                    || message === 'PHOTON_LICENSE_KEY_VALID'
                    || message === 'PHOTON_LICENSE_KEY_EXPIRING') {
                    licenseStatus = {
                        domainType: body.domain_type,
                        licenseType: body.license_type,
                        timestamp: moment().valueOf(),
                        message,
                    };

                    if(message === 'PHOTON_LICENSE_KEY_EXPIRING') {
                        commit(types.SET_LICENSE_EXPIRING, { status: true });
                    } else {
                        commit(types.SET_LICENSE_EXPIRING, { status: false });
                    }

                    storage.save('licenseStatus', licenseStatus, true);

                    return response.data.message;
                }
            })
            .catch((response) => {
                return Promise.reject(response.data.message);
            });
    },

    /**
     * Logs the user in
     *
     * @param  {function}  options.commit
     * @param  {object}  payload  API return
     * @param  {bool}  rememberMe
     * @param  {string}  redirectPath
     * @return  {void}
     */
    login({ commit }, { payload, redirectPath }) {
        api.post('auth/login', payload)
            .then((response) => {
                return _processLogin(commit, response, redirectPath);
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'AUTH');
            });
    },

    /**
     * Registers a user
     *
     * @param  {function}  options.commit
     * @param  {object}  payload
     * @param  {string}  invitationToken
     * @return  {void}
     */
    register({ commit }, { payload, invitationToken }) {
        let uri = 'auth/register';

        if (invitationToken) {
            uri = uri + '/' + invitationToken;
        }

        api.post(uri, payload)
            .then((response) => {
                if (response.data.message === 'USER_REGISTER_SUCCESS') {
                    if (invitationToken) {
                        // If there is a valid invitation token attempts to log the user in
                        response.data.message = 'USER_LOGIN_SUCCESS';

                        return _processLogin(commit, response, '/');
                    }

                    // Reroutes to registered page
                    router.push('/registered');
                }

                apiResponseCommit({ commit }, response, 'AUTH');
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'AUTH');
            });
    },

    /**
     * Checks if user is logged in (has valid token)
     *
     * @param  {function}  options.commit
     * @param  {string}  apiToken
     * @return  {void}
     */
    checkMe({commit}, { apiToken }) {

        Vue.http.headers.common['Authorization'] = 'Bearer ' + apiToken;

        return api.get('auth/me')
            .then((response) => {
                commit(types.USER_LOGIN_SUCCESS, response.data.body);

                // Refreshes token so the app knows it's TTL
                return _getRefreshToken();
            })
            .catch((response) => {
                // Delete invalid token
                delete Vue.http.headers.common['Authorization'];

                storage.remove('apiToken');

                return response;
            });
    },

    /**
     * Impersonates a user
     *
     * @param  {function}  options.commit
     * @param  {int}  options.id
     * @return  {void}
     */
    impersonateUser({commit}, { id }) {
        let uri = 'auth/impersonate/';

        // If id param is passed starts impersonating, else call impresonation stop
        const impersonateResourse = id ? id : 'stop';

        uri = uri + impersonateResourse;

        api.get(uri)
            .then((response) => {
                _refreshCallback(response);

                apiResponseCommit({
                    commit
                }, response, 'AUTH');

                api.get('auth/me')
                    .then((response) => {
                        commit(types.USER_LOGIN_SUCCESS, response.data.body);

                        router.push('/');
                    })
                    .catch((error) => {
                        pError('AUTH', error);
                    });
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'IMPERSONATE');
            });
    },

    /**
     * Confirms a user email
     *
     * @param  {function}  options.commit
     * @param  {string}  emailToken
     * @return {object}
     */
    confirmEmail({commit}, emailToken) {
        return api.get('auth/confirm/' + emailToken)
            .then((response) => {
                return response.data.message;
            })
            .catch((response) => {
                return 'EMAIL_CONFIRMATION_' + response.data.message;
            });
    },

    /**
     * Confirms a user email change
     *
     * @param  {function}  options.commit
     * @param  {string}  emailToken
     * @param  {int}  userId
     * @return {object}
     */
    confirmEmailChange({commit}, { emailToken, userId }) {
        return api.get('extension_call/users/' + userId + '/confirmEmailChange/' + emailToken)
            .then((response) => {
                return response.data.message;
            })
            .catch((response) => {
                return 'EMAIL_CONFIRMATION_' + response.data.message;
            });
    },

    /**
     * Requests a password reset
     *
     * @param  {function}  options.commit
     * @param  {object}  payload
     * @return  {void}
     */
    forgotPassword({commit}, payload) {
        api.post('password/request_reset', payload)
            .then((response) => {
                if (response.data.message === 'PASSWORD_RESET_REQUEST_SUCCESS') {
                    router.push('/confirm-password-reset');
                }
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'AUTH');
            });
    },

    /**
     * Resets a user password
     *
     * @param  {function}  options.commit
     * @param  {object}  payload
     * @return {promise}
     */
    resetPassword({commit}, payload) {
        return api.post('password/reset', payload)
            .then((response) => {
                return response.data.message;
            })
            .catch((response) => {
                return 'RESET_' + response.data.message;
            });
    },

    /**
     * Logs out a user
     *
     * @return  {void}
     */
    logout() {
        if (_refreshInterval) {
            clearInterval(_refreshInterval);
        }

        api.get('auth/logout')
            .then(() => {
                store.dispatch('user/removeSessionData');

                router.push('/login');
            });
    },

    /**
     * Removes the session data
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    removeSessionData({commit}) {
        delete Vue.http.headers.common['Authorization'];

        storage.remove('apiToken');

        commit(types.AUTH_LOGOUT);
    },

    /**
     * Commits the AUTH_CLEAR_ERRORS mutation which will clear all the errors from UI
     *
     * @param  {function}  options.commit
     * @return  {void}
     */
    clearAuthErrors({commit}) {
        commit(types.AUTH_CLEAR_ERRORS);
    }
};
