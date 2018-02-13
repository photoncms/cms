import * as types from '_/vuex/mutation-types';

import _ from 'lodash';

import Vue from 'vue';

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/assetsManagerActions';

export default function assetsManagerFactory (moduleName) {
    /**
     * Define the module state
     *
     * @type  {object}
     */
    const state = {
        /**
         * Assets displayed in assets manager picker
         *
         * @type  {Array}
         */
        assets: [],

        /**
         * Modal visibility controller (true = visible)
         *
         * @type  {boolean}
         */
        assetsManagerVisible: false,

        /**
         * Toggles the state of delete confirmation buttons
         *
         * @type  {boolean}
         */
        confirmDeleteEntry: false,

        /**
         * List of edited fields
         *
         * @type  {Array}
         */
        dirtyFields: [],

        /**
         * The currently edited entry.
         * Object changes as the form data is edited so that it can be used to persist the data in the API.
         *
         * @type  {Object}
         */
        editedEntry: {},

        /**
         * Editor mode can be either 'create', 'default' or 'edit', which affects a series of UI changes
         *
         * @type  {string}
         */
        editorMode: 'create',

        /**
         * Currently loaded entry (The original unmodified object)
         *
         * @type  {Object}
         */
        entry: {},

        /**
         * Used as a watched property to be able to refresh the form data
         *
         * @type  {boolean}
         */
        entryUpdated: false,

        /**
         * Populated by the API return data, if error happens during the save or update.
         *
         * @type  {Object}
         */
        error: {
            fields: null,
            message: null,
        },

        /**
         * Advanced search filter object
         *
         * @type  {Object}
         */
        filter: {},

        /**
         * Stores the module name
         *
         * @type {String}
         */
        moduleName,

        /**
         * Demarks if the field expects single or multiple files
         *
         * @type  {boolean}
         */
        multiple: false,

        /**
         * Stores search string
         *
         * @type  {String}
         */
        search: '',

        /**
         * Should the file be selected after it has uploaded
         *
         * @type  {boolean}
         */
        selectAfterUpload: true,

        /**
         * Stores selected assets objects
         *
         * @type  {Array}
         */
        selectedAssets: [],

        /**
         * Stores selected assets ids
         *
         * @type  {Array}
         */
        selectedAssetsIds: [],

        /**
         * Stores the info about the selected module
         *
         * @type  {Object}
         */
        selectedModule: {},

        /**
         * Currentyl selected sorting parameters
         *
         * @type  {Object}
         */
        sorting: {
            key: 'updated_at',
            raw: 'updated_at|desc',
            value: 'desc',
        },

        /**
         * Toggles the state of the submit procedure
         *
         * @type  {boolean}
         */
        submitInProgress: false,
    };

    /**
     * Define the module mutations
     *
     * @type  {object}
     */
    const mutations = {
        /**
         * Sets the error state properties
         *
         * @param  {object}  state
         * @param  {string}  options.errorMessage
         * @param  {object}  options.apiResponse
         * @return  {void}
         */
        [types.ADMIN_ERROR_DISPATCH] (state, { errorMessage, apiResponse }) {
            state.error.message = errorMessage;

            state.error.fields = apiResponse.error_fields ? apiResponse.error_fields : null;
        },

        /**
         * Clears error state properties
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.CLEAR_ADMIN_ERRORS] (state) {
            state.error = {
                fields: null,
                message: null,
            };
        },

        /**
         * Concats the results returned by API with assets state property
         *
         * @param  {object}  state
         * @param  {object}  options.values
         * @return  {void}
         */
        [types.CONCAT_ASSETS](state, { values }) {
            state.assets = state.assets.concat(values);
        },

        /**
         * Sets a confirmDeleteEntry state property
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.CONFIRM_ENTRY_DELETE] (state) {
            state.confirmDeleteEntry = !state.confirmDeleteEntry;
        },

        /**
         * Sets a series of state parameters during the entry deletion process
         *
         * @param  {object}  state
         * @param  {integer}  options.id
         * @return  {void}
         */
        [types.DELETE_DYNAMIC_MODULE_ENTRY_SUCCESS] (state, { id }) {
            state.assets = state.assets.filter(function(asset){
                if (asset.id !== id) return true;
            });
        },

        /**
         * Performs a series of state changes after a successful asset insertion
         *
         * @param  {object}  state
         * @param  {object}  options.entry
         * @return  {void}
         */
        [types.INSERT_ENTRY_SUCCESS] (state, { entry }) {
            state.assets.unshift(entry);
        },

        /**
         * Updates the entry state property.
         * Fired automatically by the apiResponseCommit method,
         * after the API returns SAVE_DYNAMIC_MODULE_ENTRY_SUCCESS message.
         *
         * @param  {object}  state
         * @param  {object}  options.entry
         * @return  {void}
         */
        [types.SAVE_DYNAMIC_MODULE_ENTRY_SUCCESS] (state, { entry }) {
            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            state.entry = entry;

            state.editedEntry = entry;

            state.editorMode = 'edit';

            state.entryUpdated = !state.entryUpdated;

            Vue.set(state.error, 'message', null);

            Vue.set(state.error, 'fields', null);
        },

        /**
         * Selects an asset
         *
         * @param  {object}  state
         * @param  {integer}  options.id
         * @return  {void}
         */
        [types.SELECT_ASSET](state, { id }) {
            if (!state.multiple) {
                if (state.selectedAssetsIds.indexOf(id) < 0) {
                    state.selectedAssetsIds = [id];
                } else {
                    state.selectedAssetsIds = [];
                }

                return;
            }

            const selectedAssetsIdsIndex = state.selectedAssetsIds.indexOf(id);

            if (selectedAssetsIdsIndex < 0) {
                // If file was not already selected, add it to selectedAssetsIds array
                state.selectedAssetsIds.push(id);
            } else {
                // If file was already selected, splice it from selectedAssetsIds array
                state.selectedAssetsIds.splice(selectedAssetsIdsIndex, 1);
            }
        },

        /**
         * Selects an active asset (the one displayed for editing in the asset manager sidebar window)
         *
         * @param  {object}  state
         * @param  {object}  options.asset
         * @return  {void}
         */
        [types.SELECT_ACTIVE_ASSET](state, { asset }) {
            state.entry = asset;

            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            state.editedEntry = state.entry;

            state.editorMode = 'edit';
        },

        /**
         * Sets the selectAfterUpload state parameter
         *
         * @param  {object}  state
         * @param  {boolean}  option.value
         * @return  {void}
         */
        [types.SELECT_AFTER_UPLOAD] (state, { value }) {
            state.selectAfterUpload = value;
        },

        /**
         * Sets the editorMode state property
         *
         * @param  {object}  state
         * @param  {boolean}  options.value
         * @return  {void}
         */
        [types.SET_ASSETS_MANAGER_EDITOR_MODE](state, { value }) {
            state.editorMode = value;
        },

        /**
         * Sets the assetsManagerVisible state property
         *
         * @param  {object}  state
         * @param  {boolean}  options.value
         * @return  {void}
         */
        [types.SET_ASSET_MANAGER_VISIBLE](state, { value }) {
            state.assetsManagerVisible = value;
        },

        /**
         * Sets the filter object
         *
         * @param  {object}  state
         * @param  {object}  options.value
         * @return  {void}
         */
        [types.SET_FILTER](state, { value }) {
            state.filter = value;
        },

        /**
         * Sets the inital state parameters
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.SET_INITIAL_STATE](state) {
            state.entry = {};

            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            state.editedEntry = state.entry;

            state.selectedAssets = [];

            state.selectedAssetsIds = [];

            state.editorMode = 'create';
        },

        /**
         * Sets the multiple state property
         *
         * @param  {object}  state
         * @param  {boolean}  options.value
         * @return  {void}
         */
        [types.SET_MULTIPLE](state, { value }) {
            state.multiple = value;
        },

        /**
         * Sets the search string
         *
         * @param  {object}  state
         * @param  {string}  options.value
         * @return  {void}
         */
        [types.SET_SEARCH](state, { value }) {
            state.search = value;
        },

        /**
         * Sets the selectedAssets state property
         *
         * @param  {object}  state
         * @param  {array}  options.value
         * @return  {void}
         */
        [types.SET_SELECTED_ASSETS](state, { value }) {
            state.selectedAssets = value;
        },

        /**
         * Repacks the value to selectedAssetsIds, so that it's type is always an array
         *
         * @param  {object}  state
         * @param  {mixed}  option.value    Can be either an integer (single file upload) or an array (multiple file upload)
         * @return  {void}
         */
        [types.SET_SELECTED_ASSETS_IDS](state, { value }) {
            if (_.isEmpty(value) && Array.isArray(value)) {
                state.selectedAssetsIds = [];

                return;
            }

            if(Array.isArray(value)) {
                state.selectedAssetsIds = value;

                return;
            }

            if (_.isObject(value) && _.has(value, 'id')) {
                state.selectedAssetsIds = [value.id];

                return;
            }

            if(value && !_.isObject(value)) {
                state.selectedAssetsIds = [value];
            }
        },

        /**
         * Set the sorting attribute
         *
         * @param  {object}  state
         * @param  {string} options.value
         * @return  {void}
         */
        [types.SET_SORTING](state, { value }) {
            const sortingOptions = value.split('|');

            state.sorting = {
                key: sortingOptions[0],
                raw: value,
                value: sortingOptions[1],
            };
        },

        /**
         * Sets the submitInProgress state boolean parameter
         *
         * @param  {object}  state
         * @param  {boolean}  options.value
         * @return  {void}
         */
        [types.SUBMIT_IN_PROGRESS] (state, { value }) {
            state.submitInProgress = value;
        },

        /**
         * Empties the assets property
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.UNSET_ASSETS](state) {
            state.assets = [];
        },

        /**
         * Prepends the results returned by API to assets state property
         *
         * @param  {object}  state
         * @param  {object}  options.values
         * @return  {void}
         */
        [types.UNSHIFT_ASSETS](state, { values }) {
            state.assets.unshift(values);
        },

        /**
         * Sets the selecteModule state property
         *
         * @param  {object}  state
         * @param  {object}  options.module
         * @return  {void}
         */
        [types.UPDATE_ASSET_SELECTED_MODULE] (state, { module }) {
            state.selectedModule = module;
        },

        /**
         * Sets dirtyFields and editedEntry state properties
         *
         * @param  {object}  state
         * @param  {string}  options.name
         * @param  {mixed}  options.newValue
         * @return  {void}
         */
        [types.UPDATE_ASSET_MODULE_ENTRY_FIELD] (state, { name, newValue }) {
            if (state.dirtyFields.indexOf(name) === -1) {
                state.dirtyFields.push(name);
            }

            Vue.set(state.editedEntry, name, newValue);
        },

        /**
         * Updates the assets state property with a new resized image object by looking for an asset by Id,
         * and than by looking for a resized image by its id
         *
         * @param  {object}  state
         * @param  {integer}  options.assetId
         * @param  {integer}  options.id
         * @param  {object}  options.resizedImage
         * @return  {void}
         */
        [types.UPDATE_RESIZED_IMAGE](state, { assetId, id, resizedImage }) {
            const updatedAssetIndex = _.findIndex(state.assets, { 'id': parseInt(assetId) });

            const updatedAssetResizedImageIndex = _.findIndex(state.assets[updatedAssetIndex].resized_images, { 'id': parseInt(id) });

            // reassign the image_size to integer instead of object
            resizedImage.image_size = resizedImage.image_size.id;

            Vue.set(state.assets[updatedAssetIndex].resized_images, updatedAssetResizedImageIndex, resizedImage);
        },
    };


    /**
     * Define the module getters
     *
     * @type  {object}
     */
    const getters = {};

    getters[moduleName] = state => state;

    return {
        actions,
        getters,
        mutations,
        namespaced: true,
        state
    };
}
