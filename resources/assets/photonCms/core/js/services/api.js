import Vue from 'vue';

import { config } from '_/config/config';

import { router } from '_/router/router';

import { store } from '_/vuex/store';

import { faviconLoading } from '_/helpers/favicon';

// import { mockResponse } from './api-mocker'; // Uncomment if using mockResponse in development

import { pLog } from '_/helpers/logger';

// Set variables used for NProgress plugin control
let _progressBarTimeout;

let _progressBarInstances = 0;

/**
 * Starts NProgress progress bar
 * @return {[type]} [description]
 */
const progressBarStart = () => {
    // Adds to progress bar instance count
    _progressBarInstances++;

    // If no NProgress progress bar already running starts one
    if (!NProgress.isStarted()) {
        NProgress.start();

        faviconLoading(true);
    }
};

/**
 * Completes NProgress progress bar process
 */
const progressBarDone = () => {
    // Subtracts from progress bar instance count (down to 0)
    _progressBarInstances = Math.max(0, _progressBarInstances - 1);

    // Clears any previous _progressBarTimeout
    clearTimeout(_progressBarTimeout);

    // Throttles progressbar stop, so it's shown at least 50ms (to smooth out subsequent API calls)
    _progressBarTimeout = setTimeout(() => {
        // If NProgress progress bar is running and there is the last instance
        if (NProgress.isStarted() && _progressBarInstances === 0) {
            // Stop progress bar
            NProgress.done();

            faviconLoading(false);
        }
    }, 50);
};

/**
 * Bootstraps API with apiBasePath and interceptor middlewares
 * @return {object} API response
 */
const _bootstrap = () => {
    Vue.http.options.root = config.ENV.apiBasePath;

    Vue.http.interceptors.push((request, next) => {
        // On any API request run progressBarStart
        progressBarStart();

        next((response) => {
            // Process response through mocker before proceeding (dev only feature)
            if (config.ENV.name === 'development') {
                // response = mockResponse(response);
            }

            // Optional low level debug log (before actions get the API responses)
            if (config.ENV.debug) {
                pLog('Resolving HTTP request', response.url, response.status, response.body.message, response.body.body);
            }

            // Handle non-responsive API
            if (response.status === 0) {
                router.push('/error/api-not-accessible');
            }

            // Handle inavlid token
            if (response.body.message === 'TOKEN_ABSENT') {

                store.dispatch('user/removeSessionData');

                router.push('/error/invalid-token');
            }

            progressBarDone();

            return response;
        });
    });

};

/**
 * Returns Vue.http method based off given arguments
 * @param  {string} method e.g. 'get' or 'post'
 * @param  {string} url
 * @param  {object} args
 * @return {object} API return
 */
const _request = (method, url, args, headers = null) => {
    return Vue.http[method](url, args, headers);
};

/**
 * Exposed api service methods
 * @type {Object}
 */
export const api = {
    bootstrap: _bootstrap,
    get: (url, args, headers = null) => {
        return _request('get', url, args, headers);
    },
    post: (url, args, headers = null) => {
        return _request('post', url, args, headers);
    },
    put: (url, args, headers = null) => {
        return _request('put', url, args, headers);
    },
    delete: (url, args, headers = null) => {
        return _request('delete', url, args, headers);
    },
    progressBarDone: progressBarDone,
    progressBarStart: progressBarStart,
};
