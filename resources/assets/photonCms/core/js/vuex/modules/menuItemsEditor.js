import * as types from '_/vuex/mutation-types';

import { store } from '_/vuex/store';

import Vue from 'vue';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    /**
     * All menus as returned by the GET menus/ API call
     *
     * @type  {Array}
     */
    allMenus: [],

    /**
     * Toggles the state of the bootstrap procedure
     *
     * @type  {boolean}
     */
    bootstrapInProgress: false,

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
     * Used as a menu_name_or_id parameter when editing a menu item
     *
     * @type  {object}
     */
    selectedMenu: {},

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
     * Resets error state object to null
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.CLEAR_MENU_ITEMS_EDITOR_ERRORS] (state) {
        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },

    /**
     * Reverses the confirmDeleteEntryItem state boolean parameter
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.CONFIRM_MENU_ITEM_DELETE] (state) {
        state.confirmDeleteEntry = !state.confirmDeleteEntry;
    },

    /**
     * Reverses the createAnother state boolean parameter
     *
     * @param  {object}  state
     * @param  {boolean}  options.value
     * @return  {void}
     */
    [types.CREATE_ANOTHER_MENU_ITEM] (state, { value }) {
        state.createAnother = value;
    },

    /**
     * Fired automatically by the apiResponseCommit method, after the API returns CREATE_MENU_ITEM_SUCCESS message.
     * Does nothing as the mutations are handled by the submitEntry action.
     *
     * @return  {void}
     */
    [types.CREATE_MENU_ITEM_SUCCESS] () {
        return;
    },

    /**
     * Fired automatically by the apiResponseCommit method, after the API returns DELETE_MENU_ITEM_SUCCESS message.
     * Does nothing as the mutations are handled by the submitEntry action.
     *
     * @return  {void}
     */
    [types.DELETE_MENU_ITEM_SUCCESS] () {
        return;
    },

   /**
     * Stores all menus info
     *
     * @param  {object}  state
     * @param  {object}  options.allMenus
     * @return  {void}
     */
    [types.LOAD_ALL_MENUS_SUCCESS] (state, { allMenus }) {
        state.allMenus = allMenus;
    },

   /**
     * Performs a series of changes upon successful menu load.
     * Fired automatically by the apiResponseCommit method, after the API returns LOAD_MENU_ITEM_SUCCESS message.
     *
     * @param  {object}  state
     * @param  {object}  options.menu
     * @return  {void}
     */
    [types.LOAD_MENU_ITEM_SUCCESS] (state, { menu_item }) {
        initStateUponLoadMenuItemSuccess(state, menu_item);
    },

    /**
     * Sets the bootstrapInProgress state boolean parameter
     *
     * @param  {object}  state
     * @param  {boolean}  options.value
     * @return  {void}
     */
    [types.MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS] (state, { value }) {
        state.bootstrapInProgress = value;
    },

    /**
     * Sets the submitInProgress state boolean parameter
     *
     * @param  {object}  state
     * @param  {boolean}  options.value
     * @return  {void}
     */
    [types.MENU_ITEMS_EDITOR_SUBMIT_IN_PROGRESS] (state, { value }) {
        state.submitInProgress = value;
    },

    /**
     * Updates the error state object.
     *
     * @param  {object}  state
     * @param  {object}  options.apiResponse
     * @param  {string}  options.errorMessage
     * @return  {void}
     */
    [types.MENU_ITEMS_EDITOR_ERROR_DISPATCH] (state, { apiResponse, errorMessage }) {
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
    [types.MENU_ITEMS_EDITOR_SET_CREATE_ENTRY_UI] (state) {
        state.dirtyFields = [];

        state.entry = {};

        state.confirmDeleteEntry = false;

        const template = {
            adminEntry: [],
            menu_link_type_id: [],
            adminModule: [],
            menu_id: state.selectedMenu.id,
        };

        state.editedEntry = template;

        state.editorMode = 'create';

        Vue.set(state.error, 'message', null);

        Vue.set(state.error, 'fields', null);
    },

    /**
     * Sets the state.editedEntry.adminEntry property to []
     *
     * @param  {object}  state
     * @return  {void}
     */
    [types.UNSET_MENU_ITEM_ADMIN_ENTRY] (state) {
        Vue.set(state.editedEntry, 'adminEntry', []);
    },

    /**
     * Sets dirtyFields and editedEntry state properties
     *
     * @param  {object}  state
     * @param  {string}  options.name
     * @param  {mixed}  options.newValue
     * @return  {void}
     */
    [types.UPDATE_MENU_ITEM_FIELD] (state, { name, newValue }) {
        if (state.dirtyFields.indexOf(name) === -1) {
            state.dirtyFields.push(name);
        }

        Vue.set(state.editedEntry, name, newValue);

        if(['adminModule', 'adminEntry', 'staticLink'].indexOf(name) !== -1
            && state.dirtyFields.indexOf('resource_data') === -1) {
            state.dirtyFields.push('resource_data');
        }

        if (name === 'adminModule') {
            const selectedModule = store.state.photonModule.moduleIdToTableNameMap[newValue];

            Vue.set(state.selectedModule, 'table_name', selectedModule);
        }
    },

    /**
     * Sets the selectedNode state property
     */
    [types.UPDATE_MENU_ITEM_SELECTED_NODE](state, { anchorText, url }) {
        Vue.set(state.selectedNode, 'anchorText', anchorText);

        Vue.set(state.selectedNode, 'url', url);
    },

    /**
     * Updates the entry state property.
     * Fired automatically by the apiResponseCommit method, after the API returns UPDATE_MENU_ITEM_SUCCESS message.
     *
     * @param  {object}  state
     * @param  {object}  options.menu_item
     * @return  {void}
     */
    [types.UPDATE_MENU_ITEM_SUCCESS] (state, { menu_item }) {
        state.bootstrapInProgress = true;

        initStateUponLoadMenuItemSuccess(state, menu_item);

        state.entryUpdated = !state.entryUpdated;

        state.bootstrapInProgress = false;
    },

    /**
     * Sets the selectedMenu property
     *
     * @param  {object}  state
     * @param  {object}  options.selectedMenu
     * @return  {void}
     */
    [types.UPDATE_SELECTED_MENU] (state, { selectedMenu }) {
        state.selectedMenu = selectedMenu;
    },
};

/**
 * Initializes state upon successful menu item loading
 * Can happen either through read or update
 *
 * @param   {object}  state
 * @param   {object}  menu_item
 * @return  {void}
 */
function initStateUponLoadMenuItemSuccess(state, menu_item) {
    state.dirtyFields = [];

    menu_item.menu_link_type_id = menu_item.menu_link_type.id;

    if(menu_item.menu_link_type_id === 1) {
        menu_item.adminModule = JSON.parse(menu_item.resource_data);
    }

    if(menu_item.menu_link_type_id === 2) {
        menu_item.staticLink = menu_item.resource_data;
    }

    if(menu_item.menu_link_type_id === 4) {
        menu_item.adminModule = JSON.parse(menu_item.resource_data);

        menu_item.adminEntry = JSON.parse(menu_item.entry_data);

        Vue.set(state.selectedModule, 'table_name', menu_item.adminModule.table_name);
    }

    state.entry = menu_item;

    state.confirmDeleteEntry = false;

    state.editedEntry = menu_item;

    state.editorMode = 'edit';

    Vue.set(state.error, 'message', null);

    Vue.set(state.error, 'fields', null);
}

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    menuItemsEditor: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/menuItemsEditorActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
