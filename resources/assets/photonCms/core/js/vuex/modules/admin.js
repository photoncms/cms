import * as types from '_/vuex/mutation-types';

import _ from 'lodash';

import Vue from 'vue';

import { mapFromId } from '_/services/fieldTypes';

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/adminActions';

export default function adminFactory (moduleName) {
    /**
     * Define the module state
     *
     * @type  {object}
     */
    const state = {
        /**
         * Asset manager search filter is used to preserve the search filter for any asset manager modal
         * on a per-photon-module basis. Navigating to another module would reset this search filter.
         *
         * @type  {Object}
         */
        assetManagerSearchFilter: {},

        /**
         * Toggles the state of delete confirmation buttons
         *
         * @type  {boolean}
         */
        confirmDeleteEntry: false,

        /**
         * Toggles the state of createAnother checkbox
         *
         * @type  {boolean}
         */
        createAnother: false,

        /**
         * List of edited fields
         *
         * @type  {Array}
         */
        dirtyFields: [],

        /**
         * Stops the automatic slug generation
         *
         * @type  {Boolean}
         */
        disableAutomaticSlugGeneration: false,

        /**
         * The currently edited entry.
         * Object changes as the form data is edited so that it can be used to persist the data in the API.
         *
         * @type  {Object}
         */
        editedEntry: {},

        /**
         * Editor mode can be either 'create', 'default', 'edit', or 'search' which affects a series of UI changes
         *
         * @type  {string}
         */
        editorMode: 'default',

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
         * Contains lazy loaded nodes, to be used with non-sortable module types in jsTree
         *
         * @type  {Array}
         */
        lazyLoadedNodes: [],

        /**
         * Stores this module name
         */
        moduleName,

        /**
         * Stores the info about the selected module
         *
         * @type  {Object}
         */
        selectedModule: {},

        /**
         * Stores the info about the currently selected module fields
         *
         * @type  {Object}
         */
        selectedModuleFields: {},

        /**
         * Stores the node selected in the jsTree
         *
         * @type  {Object}
         */
        selectedNode: {},

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
         * Sets a series of state properties during the admin mode change
         *
         * @param  {object}  state
         * @param  {string}  options.newEditorMode
         * @return  {void}
         */
        [types.CHANGE_ADMIN_MODE] (state, { newEditorMode }) {
            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            if (newEditorMode === 'default') {
                state.editedEntry = {};
            }

            state.editorMode = newEditorMode;
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
         * Concats the results returned by API
         *
         * @param  {object}  options.values
         * @return  {void}
         */
        [types.CONCAT_LAZY_LOADED_NODES](state, { values }) {
            state.lazyLoadedNodes = state.lazyLoadedNodes.concat(values);
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
         * Reverses the createAnother state boolean parameter
         *
         * @param  {object}  state
         * @param  {boolean}  option.value
         * @return  {void}
         */
        [types.CREATE_ANOTHER] (state, { value }) {
            state.createAnother = value;
        },

        /**
         * Reverses the disableAutomaticSlugGeneration state boolean parameter
         *
         * @param  {object}  state
         * @param  {boolean}  option.value
         * @return  {void}
         */
        [types.DISABLE_AUTOMATIC_SLUG_GENERATION] (state, { value }) {
            state.disableAutomaticSlugGeneration = value;
        },

        /**
         * Empties the lazyLoadedNodes state property
         *
         * @return  {void}
         */
        [types.EMPTY_LAZY_LOADED_NODES](state) {
            state.lazyLoadedNodes = [];
        },

        /**
         * Sets a series of state properies during the entry creation process
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.SET_CREATE_ENTRY_UI] (state) {
            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            const templateEntry = {
                anchor_text: null,
            };

            state.editedEntry = templateEntry;

            // Prevents the root item creation for all modules that have a category parent module
            if (state.selectedModule.category) {
                state.editorMode = 'default';

                return;
            }

            state.editorMode = 'create';
        },

        /**
         * Sets a series of state parameters during the entry deletion process
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.DELETE_DYNAMIC_MODULE_ENTRY_SUCCESS] (state) {
            Vue.delete(state, 'entry');

            const templateEntry = {
                anchor_text: null,
            };

            state.editedEntry = templateEntry;
        },

        /**
         * Sets the entry property
         *
         * @param  {object}  state
         * @param  {object}  options.entry
         * @return  {void}
         */
        [types.LOAD_DYNAMIC_MODULE_ENTRY_SUCCESS] (state, { entry }) {
            state.dirtyFields = [];

            state.entry = entry;

            state.confirmDeleteEntry = false;

            state.editedEntry = entry;

            state.editorMode = 'edit';

            Vue.set(state.error, 'message', null);

            Vue.set(state.error, 'fields', null);
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
         * Updates the entry state property.
         * Fired automatically by the apiResponseCommit method,
         * after the API returns SAVE_DYNAMIC_MODULE_ENTRIES_SUCCESS message.
         *
         * @param  {object}  state
         * @param  {object}  options.entry
         * @return  {void}
         */
        [types.SAVE_DYNAMIC_MODULE_ENTRIES_SUCCESS] (state, { entries }) {
            const lastEntry = entries[entries.length - 1];

            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            state.entry = lastEntry;

            state.editedEntry = lastEntry;

            state.editorMode = 'edit';

            state.entryUpdated = !state.entryUpdated;

            Vue.set(state.error, 'message', null);

            Vue.set(state.error, 'fields', null);
        },

        /**
         * Sets the assetManagerSearchFilter state property
         *
         * @param  {object}  state
         * @param  {object}  options.value
         * @return  {void}
         */
        [types.SET_ASSET_MANAGER_SEARCH_FILTER] (state, { value }) {
            Vue.set(state, 'assetManagerSearchFilter', value);
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
         * Toggles the entryUpdated property to force the refresh of form fields
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.TOGGLE_ENTRY_UPDATED] (state) {
            state.entryUpdated = !state.entryUpdated;
        },

        /**
         * Sets the selectedModuleFields state property
         *
         * @param  {object}  state
         * @param  {object}  options.selectedModule
         * @return  {void}
         */
        [types.UPDATE_ADMIN_SELECTED_MODULE] (state, { selectedModule }) {
            if (!_.isEmpty(selectedModule)) {
                selectedModule.fields.forEach((field) => {
                    field.typeMeta = mapFromId[parseInt(field.type, 10)];

                    if (field.unique_name) {
                        state.selectedModuleFields[field.unique_name] = field;
                    }
                });
            }

            state.selectedModule = selectedModule;
        },

        /**
         * Sets a series of state properties during the entry update process
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.UPDATE_ENTRY] (state) {
            state.confirmDeleteEntry = false;

            state.dirtyFields = [];

            state.editedEntry = state.entry;

            state.editorMode = 'edit';
        },

        /**
         * Sets dirtyFields and editedEntry state properties
         *
         * @param  {object}  state
         * @param  {string}  options.name
         * @param  {mixed}  options.newValue
         * @return  {void}
         */
        [types.UPDATE_MODULE_ENTRY_FIELD] (state, { name, newValue }) {
            if (state.dirtyFields.indexOf(name) === -1) {
                state.dirtyFields.push(name);
            }

            Vue.set(state.editedEntry, name, newValue);
        },

        /**
         * Sets the selectedNode state property
         *
         * @param  {object}  state
         *
         * @param   {string}  options.anchorText
         * @param   {integer}  options.parentId
         * @param   {integer}  options.scopeId
         * @param   {string}  options.url
         * @return  {void}
         */
        [types.UPDATE_SELECTED_NODE](state, { anchorText, parentId, scopeId, url }) {
            Vue.set(state.selectedNode, 'anchorText', anchorText);

            Vue.set(state.selectedNode, 'url', url);

            if (parentId) {
                state.editedEntry['parent_id'] = parentId;
            } else {
                Vue.delete(state.editedEntry, 'parent_id');
            }

            if (scopeId) {
                state.editedEntry['scope_id'] = scopeId;
            } else {
                Vue.delete(state.editedEntry, 'scope_id');
            }

            if (state.selectedModule != 'non_sortable'
                && (scopeId != null || parentId != null)) {
                state.editorMode = 'create';

                return;
            }

            state.editorMode = 'default';
        },
    };

    /**
     * Define the module getters
     *
     * @type {object}
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
