import * as mutationTypes from '_/vuex/mutation-types';

import * as notificationTypes from '_/services/notification-types';

import { store } from '_/vuex/store';

import { pError } from '_/helpers/logger';

/**
 * Generic handler to forward successful API call message and payload as a commit
 * @param  {function} options.commit
 * @param  {object} apiResponse
 * @return {object}
 */
export const apiResponseCommit = ({commit}, apiResponse) => {
    // If something is missing log and return
    if (!apiResponse || !apiResponse.data) {
        pError('No data in response', apiResponse);

        return apiResponse;
    }

    // Shorten apiResponse object to include only response data
    apiResponse = apiResponse.data;

    // If the message is a notification type, send out a notification
    if (notificationTypes[apiResponse.message] !== undefined) {
        store.dispatch('notification/addNotifications', { notifications: [notificationTypes[apiResponse.message]] });
    }

    // If the message is not a mutation type log an error and return
    if (mutationTypes[apiResponse.message] === undefined) {
        pError(apiResponse.message + ' mutation is not defined!');

        return apiResponse;
    }

    commit(mutationTypes[apiResponse.message], apiResponse.body);

    return apiResponse;
};

/**
 * Generic handler to forward errored API call message and payload as a commit
 * @param  {function} options.commit
 * @param  {object} apiResponse
 * @param  {string} nameSpace
 * @return {object}
 */
export const errorCommit = ({ commit }, apiResponse, nameSpace) => {
    if (!apiResponse || !apiResponse.data) {
        pError('No data in error response', apiResponse);

        return apiResponse;
    }

    // Shorten apiResponse object to include only response data
    apiResponse = apiResponse.data;

    // Create commit type by combining namespace and '_ERROR_DISPATCH' string
    const errorCommitType = nameSpace + '_ERROR_DISPATCH';

    // Create error message by combining namespace and message returned by API
    const errorMessage = nameSpace + '_' + apiResponse.message;

    // If the message is a notification type, send out a notification
    if (notificationTypes[apiResponse.message] !== undefined) {
        store.dispatch('notification/addNotifications', { notifications: [notificationTypes[apiResponse.message]] });
    }

    // If the message is not a mutation type log an error and return
    if (mutationTypes[errorCommitType] === undefined) {
        pError(errorCommitType + ' mutation is not defined!');

        return apiResponse;
    }

    commit(mutationTypes[errorCommitType], {
        apiResponse: apiResponse.body,
        errorMessage,
    });

    return apiResponse;
};
