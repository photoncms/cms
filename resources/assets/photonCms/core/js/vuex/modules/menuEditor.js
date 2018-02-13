import * as types from '_/vuex/mutation-types';

import Vue from 'vue';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    /**
     * Toggles the state of delete confirmation buttons
     *
     * @type  {boolean}
     */
    confirmDeleteEntry: true,

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
     * The currently edited entry.
     * Object changes as the form data is edited so that it can be used to persist the data in the API.
     *
     * @type  {Object}
     */
    editedEntry: {},

    /**
     * Editor mode can be either 'create' or 'edit', which affects a series of UI changes
     *
     * @type  {string}
     */
    editorMode: 'create',

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
     * Temporary solution until the permission_control object is implemented
     *
     * @type  {Object}
     */
    selectedModule: {
        permission_control: {
            crud: {
                create: true,
            }
        }
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
     * Resets error state object to null
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.CLEAR_MENU_EDITOR_ERRORS] (state) {
        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },

    /**
     * Reverses the confirmDeleteEntryItem state boolean parameter
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.CONFIRM_MENU_DELETE] (state) {
        state.confirmDeleteEntry = !state.confirmDeleteEntry;
    },

    /**
     * Reverses the createAnother state boolean parameter
     *
     * @param  {object}  state
     * @param  {boolean}  options.value
     * @return  {void}
     */
    [types.CREATE_ANOTHER_MENU] (state, { value }) {
        state.createAnother = value;
    },

    /**
     * Fired automatically by the apiResponseCommit method, after the API returns CREATE_MENU_SUCCESS message.
     * Does nothing as the mutations are handled by the submitEntry action.
     *
     * @return  {void}
     */
    [types.CREATE_MENU_SUCCESS] () {
        return;
    },

    /**
     * Fired automatically by the apiResponseCommit method, after the API returns DELETE_MENU_SUCCESS message.
     * Does nothing as the mutations are handled by the submitEntry action.
     *
     * @return  {void}
     */
    [types.DELETE_MENU_SUCCESS] () {
        return;
    },

   /**
     * Performs a series of changes upon successful menu load.
     * Fired automatically by the apiResponseCommit method, after the API returns LOAD_MENU_SUCCESS message.
     *
     * @param  {object}  state
     * @param  {object}  options.menu
     * @return  {void}
     */
    [types.LOAD_MENU_SUCCESS] (state, { menu }) {
        state.dirtyFields = [];

        state.entry = menu;

        state.confirmDeleteEntry = false;

        state.editedEntry = menu;

        state.editorMode = 'edit';

        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },

    /**
     * Updates the error state object.
     *
     * @param  {object}  state
     * @param  {object}  options.apiResponse
     * @param  {string}  options.errorMessage
     * @return  {void}
     */
    [types.MENU_EDITOR_ERROR_DISPATCH] (state, { apiResponse, errorMessage }) {
        Vue.set(state.error, 'message', errorMessage);

        const fields = apiResponse.error_fields ? apiResponse.error_fields : null;

        Vue.set(state.error, 'fields', fields);
    },

    /**
     * Sets a series of state properies to configure the UI for new entry creation (empty form)
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.MENU_EDITOR_SET_CREATE_ENTRY_UI] (state) {
        state.dirtyFields = [];

        state.entry = {};

        state.confirmDeleteEntry = false;

        state.editedEntry = {};

        state.editorMode = 'create';

        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },

    /**
     * Sets the submitInProgress state boolean parameter
     *
     * @param  {object}  state
     * @param  {boolean}  options.value
     * @return  {void}
     */
    [types.MENU_EDITOR_SUBMIT_IN_PROGRESS] (state, { value }) {
        state.submitInProgress = value;
    },

    /**
     *
     * Sets dirtyFields and editedEntry state properties
     *
     * @param  {object}  state
     * @param  {string}  options.name
     * @param  {mixed}  options.newValue
     * @return  {void}
     */
    [types.UPDATE_MENU_FIELD] (state, { name, newValue }) {
        if (state.dirtyFields.indexOf(name) === -1) {
            state.dirtyFields.push(name);
        }

        Vue.set(state.editedEntry, name, newValue);
    },

    /**
     * Updates the entry state property.
     * Fired automatically by the apiResponseCommit method, after the API returns UPDATE_MENU_SUCCESS message.
     *
     * @param  {object}  state
     * @param  {object}  options.menu
     * @return  {void}
     */
    [types.UPDATE_MENU_SUCCESS] (state, { menu }) {
        state.confirmDeleteEntry = false;

        state.dirtyFields = [];

        state.entry = menu;

        state.editedEntry = menu;

        state.editorMode = 'edit';

        state.entryUpdated = !state.entryUpdated;

        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },
};

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    menuEditor: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/menuEditorActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
