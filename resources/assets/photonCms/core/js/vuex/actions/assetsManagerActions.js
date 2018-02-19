import * as types from '_/vuex/mutation-types';

import _ from 'lodash';

import { api } from '_/services/api';

import {
    apiResponseCommit,
    errorCommit
} from '_/vuex/actions/commonActions';

const photonConfig = require('~/config/config.json');

export default {
    /**
     * Sets the asset manager modal window visibility
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    assetsManagerVisible({ commit }, { value }) {
        commit(types.SET_ASSET_MANAGER_VISIBLE, { value });
    },

    /**
     * Clears the admin UI from errors
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearAdminErrors({commit}) {
        commit('CLEAR_ADMIN_ERRORS');
    },

    /**
     * Resets the file input value
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearAllSelectedAssets({ commit }) {
        commit(types.SET_SELECTED_ASSETS_IDS, { value: [] });

        commit(types.SET_SELECTED_ASSETS, { value: [] });
    },

    /**
     * Commits a CONFIRM_ENTRY_DELETE which switches the ui to delete confirmation mode
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    confirmDeleteEntry ({ commit }) {
        commit(types.CONFIRM_ENTRY_DELETE);
    },

    /**
     * Deletes an entry
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {integer}  options.entryId
     * @param   {string}  options.selectedModuleName
     * @return  {promise}
     */
    deleteEntry ({ commit, dispatch, state }, { entryId, selectedModuleName }) {
        if (!state.confirmDeleteEntry) {
            return;
        }

        dispatch('setSubmitEntryInProgress', { value: true });

        return api.delete(`${selectedModuleName}/${entryId}`)
            .then(() => {
                commit(types.DELETE_DYNAMIC_MODULE_ENTRY_SUCCESS, { id: entryId });

                commit(types.SET_INITIAL_STATE);

                dispatch('setSubmitEntryInProgress', { value: false });
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'ADMIN');

                dispatch('setSubmitEntryInProgress', { value: false });
            });
    },

    /**
     * Commits the errorCommit method
     * Used in drop zone asset uploading process
     *
     * @param   {function}  options.commit
     * @param   {object}  options.apiResponse
     * @param   {string}  options.nameSpace
     * @return  {object}
     */
    errorCommit ({ commit }, { apiResponse, nameSpace }) {
        return errorCommit({ commit }, apiResponse, nameSpace);
    },

    /**
     * Gets selected files from the API
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {array}  options.selectedAssetsIds
     * @param   {boolean}  options.tryLoadingFromCache
     * @return  {object}
     */
    getSelectedAssets({ commit, state }, { selectedAssetsIds = [] } = {}) {
        if (_.isEmpty(selectedAssetsIds)) {
            commit(types.SET_SELECTED_ASSETS, { value: [] });

            return;
        }

        if (state.multiple) {
            selectedAssetsIds = selectedAssetsIds.map((asset) => {
                return asset;
            }).join(',');
        }

        const payload = {
            filter: {
                id: {
                    in: selectedAssetsIds,
                },
            },
        };

        return api.post('filter/assets', payload)
            .then((response) => {
                commit(types.SET_SELECTED_ASSETS, { value: response.data.body.entries });

                return response.data.body.entries;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'ADMIN');
            });
    },

    /**
     * Initializes the essential asset manager state object parameters
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.multiple
     * @param   {mixed}  options.value  Value can be an integer (single file upload) or an array (multiple file upload)
     * @return  {void}
     */
    initializeState({ commit }, { multiple, value }) {
        commit(types.SET_INITIAL_STATE);

        commit(types.SET_MULTIPLE, { value: multiple });

        commit(types.UNSET_ASSETS);

        if(multiple && !_.isEmpty(value)) {
            value = value.map((asset) => {
                if(_.has(asset, 'id')) {
                    return asset['id'];
                }
            });
        }

        commit(types.SET_SELECTED_ASSETS_IDS, { value });
    },

    /**
     * Emits the event upon hitting bottom of an assets container
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @return  {void}
     */
    loadPaginatedAssets({ commit, state }) {
        const assetsLength = state.assets.length > 0 ? state.assets.length : 0;

        const itemsPerPage = photonConfig.assetManager.itemsPerPage;

        // Handle the case where total number of assets is lower than itemsPerPage
        if (assetsLength > 0 && assetsLength < itemsPerPage) {
            return Promise.reject();
        }

        const payload = {
            pagination: {
                current_page: Math.ceil(assetsLength / itemsPerPage) + 1,
                items_per_page: itemsPerPage,
            },
            sorting: {
                [state.sorting.key]: state.sorting.value
            }
        };

        payload.filter = state.filter;

        return api.post('filter/assets', payload)
            .then((response) => {
                commit(types.CONCAT_ASSETS, { values: response.data.body.entries });

                if(response.data.body.pagination.last_page <= payload.pagination.current_page) {
                    return Promise.reject(response);
                }

                return Promise.resolve(response);
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Readies the model for new file upload
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    prepareForNewUpload({ commit }) {
        commit(types.SET_INITIAL_STATE);
    },

    /**
     * Performs an image resize
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.id
     * @param   {integer}  options.x
     * @param   {integer}  options.y
     * @param   {integer}  options.width
     * @param   {integer}  options.height
     * @return  {void}
     */
    resizeImage({ commit }, { id, x, y, width, height }) {
        return api.get(`extension_call/resized_images/${id}/rebuild/${x}/${y}/${width}/${height}`)
            .then((response) => {
                const resizedImage = response.data.body.resized_image;

                commit(types.UPDATE_RESIZED_IMAGE, { assetId: resizedImage.image.id, id: resizedImage.id, resizedImage });

                return Promise.resolve();
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Commits a SELECT_AFTER_UPLOAD which toggles the selection upon upload
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    selectAfterUpload ({ commit }, { value }) {
        commit(types.SELECT_AFTER_UPLOAD, { value });
    },

    /**
     * Performs an asset selection
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {object}  options.asset
     * @return  {void}
     */
    selectAsset({ commit, dispatch, state }, { asset, selectActiveAsset = true } = {}) {
        commit(types.CLEAR_ADMIN_ERRORS);

        commit(types.SELECT_ASSET, { id: asset.id });

        if (selectActiveAsset) {
            commit(types.SELECT_ACTIVE_ASSET, { asset });
        }

        // Populate the selectedAssets data property
        return dispatch('getSelectedAssets', {
            selectedAssetsIds: state.selectedAssetsIds,
            tryLoadingFromCache: true,
        });
    },

    /**
     * Sets the editorMode state property
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    setAssetsManagerEditorMode ({ commit }, { value }) {
        commit(types.SET_ASSETS_MANAGER_EDITOR_MODE, { value });
    },

    /**
     * Sets the search string
     *
     * @param  {function}  options.commit
     * @param  {object}  options.value
     * @return  {void}
     */
    setFilterObject({ commit }, { value }) {
        commit(types.SET_FILTER, { value });

        commit(types.SET_SEARCH, { value: '' });

        commit(types.UNSET_ASSETS);
    },

    /**
     * Sets the search string
     *
     * @param  {function}  options.commit
     * @param  {string}  options.value
     * @return  {void}
     */
    setSearchString({ commit }, { value }) {
        commit(types.SET_SEARCH, { value });

        commit(types.SET_FILTER, { value: {} });

        commit(types.UNSET_ASSETS);
    },

    /**
     * Sets the sorting option
     *
     * @param  {function}  options.commit
     * @param  {string}  options.value
     * @return  {void}
     */
    setSortingOption({ commit }, { value }) {
        commit(types.SET_SORTING, { value });

        commit(types.UNSET_ASSETS);
    },

    /**
     * Sets the submitInProgress state property
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    setSubmitEntryInProgress ({ commit }, { value }) {
        commit(types.SUBMIT_IN_PROGRESS, { value });
    },

    /**
     * Submits (POST or PUT) entry to API
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {promise}
     */
    submitEntry({ commit, dispatch, state }) {
        const selectedModuleName = state.selectedModule.table_name;

        let action = 'post';

        let uri = selectedModuleName;

        // Makes a copy of the entry so the state object doesn't get mutated
        let payload = Object.assign({}, state.editedEntry);

        // Handle the asset manager special case, as it needs to use multipart/form-data
        if (state.editorMode === 'create') {
            let formData = new FormData();

            _.forEach(payload, function(value, key) {
                formData.append(key, value);
            });

            payload = formData;
        }

        if (state.editorMode === 'edit') {
            action = 'put';

            uri = `${uri}/${payload.id}`;

            /**
             * Clean up PUT payload to only include fields which are dirty
             * (have been manipulated by the user in some way)
             */
            for (const key in payload) {
                // If entry key is not in the array of dirty fields, remove it from payload
                if (payload.hasOwnProperty(key) && state.dirtyFields.indexOf(key) === -1) {
                    delete payload[key];
                }
            }
        }

        return api[action](uri, payload)
            .then((response) => {
                apiResponseCommit({ commit }, response);

                if (action === 'post') {
                    commit(types.INSERT_ENTRY_SUCCESS, { entry: response.data.body.entry });

                    if(state.selectAfterUpload) {
                        dispatch('selectAsset', { asset: response.data.body.entry });
                    }
                }

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({commit}, response, 'ADMIN');

                dispatch('setSubmitEntryInProgress', { value: false });
            });
    },

    /**
     * Updates assets entries
     *
     * @param   {function}  options.commit
     * @param   {object}  options.values
     * @return  {void}
     */
    updateAssetsEntries({ commit }, { values }) {
        commit(types.UNSHIFT_ASSETS, { values });
    },

    /**
     * Sets the slectedModule state property
     *
     * @param   {function}  options.commit
     * @param   {object}  options.module
     * @return  {void}
     */
    updateSelectedModule({ commit }, { module }) {
        commit(types.UPDATE_ASSET_SELECTED_MODULE, { module });
    },
};
