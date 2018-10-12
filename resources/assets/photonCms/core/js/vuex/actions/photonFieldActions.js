import { api } from '_/services/api';

import {
    apiResponseCommit,
    errorCommit,
} from '_/vuex/actions/commonActions';

import { pError } from '_/helpers/logger';

import * as types from '_/vuex/mutation-types';

export default {
    /**
     * Retrieves an array of fields.
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {boolean}  refreshList  Forces data pull from API, instead of getting the cached version from the state.
     * @return  {promise}
     */
    getPhotonFields({ commit, state }, { refreshList = false } = {}) {
        if (state.photonField && !refreshList) {
            return new Promise(function(resolve) {
                resolve(state.photonField);
            });
        }

        return api.get('field_types')
            .then((response) => {
                apiResponseCommit({ commit }, response, 'FIELDS');
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'FIELDS');
            });
    },

    /**
     * Retrieves all field groups.
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {boolean}  refreshList  Forces data pull from API, instead of getting the cached version from the state.
     * @return  {promise}
     */
    getPhotonFieldGroups({ commit, state }, { refreshList = false } = {}) {
        if (state.photonField && !refreshList) {
            return new Promise(function(resolve) {
                resolve(state.photonField);
            });
        }

        const payload = {
            include_relations: false,
            sorting: {
                lft: 'asc',
            }
        };

        return api.post('filter/field_groups', payload)
            .then((response) => {
                commit(types.GET_ALL_FIELD_GROUPS_SUCCESS, { response });
            }, () => {
                pError('Failed to fetch field groups.', response);

                return Promise.reject(response);
            });
    },
};
