import * as types from '_/vuex/mutation-types';

import { api } from '_/services/api';

import { errorCommit } from '_/vuex/actions/commonActions';

import { pWarn } from '_/helpers/logger';

export default {
    /**
     * Subscribes a user to the entry
     *
     * @param   {[type]}  options.commit  [description]
     * @param   {[type]}  options.dispatch  [description]
     * @param   {[type]}  options.state  [description]
     * @param   {[type]}  options.entryId  [description]
     * @param   {[type]}  options.moduleTableName  [description]
     * @param   {[type]}  options.shouldSubscribe  [description]
     * @return  {[type]}  [description]
     */
    subscribe({ commit, dispatch, state }, { entryId, moduleTableName }) {
        const alreadySubscribed = isAlreadySubscribed(state, entryId, moduleTableName);

        if (alreadySubscribed) {
            pWarn(
                'subscriptionActions.js',
                'subscribe()',
                'ALREADY SUBSCRIBED'
            );
            return;
        }

        return api.post(`subscribe/${moduleTableName}/${entryId}`)
            .then((response) => {
                commit(types.SUBSCRIBE, { entryId, moduleTableName });

                commit(types.SET_SUBSCRIBED_USERS, { subscribedUsers: response.data.body.listOfSubscribedUsers });

                pWarn(
                    'subscriptionActions.js',
                    'subscribe()',
                    'SUCESSFULLY SUBSCRIBED'
                );
            })
            .catch((response) => {
                return errorCommit({commit}, response, 'SUBSCRIBE');
            });
    },

    unsubscribe({ commit, dispatch, state }, { entryId, moduleTableName }) {
        const alreadySubscribed = isAlreadySubscribed(state, entryId, moduleTableName);

        if (alreadySubscribed) {
            pWarn(
                'subscriptionActions.js',
                'unsubscribe()',
                'ALREADY SUBSCRIBED'
            );
        }

        if (!(state.entryId && state.moduleTableName)) {
            pWarn(
                'subscriptionActions.js',
                'unsubscribe()',
                'SUBSCRIPTION DOESNT EXIST',
                state
            );

            return;
        }

        return api.delete(`subscribe/${moduleTableName}/${entryId}`)
            .then(() => {
                commit(types.UNSUBSCRIBE);

                commit(types.UNSET_SUBSCRIBED_USERS);

                pWarn(
                    'subscriptionActions.js',
                    'subscribe()',
                    'SUCESSFULLY UNSUBSCRIBED'
                );
            })
            .catch((response) => {
                return errorCommit({commit}, response, 'SUBSCRIBE');
            });
    },
};

const isAlreadySubscribed = function isAlreadySubscribed (state, entryId, moduleTableName) {
    if (state.entryId === entryId && state.moduleTableName === moduleTableName) {
        return true;
    }

    return false;
};
