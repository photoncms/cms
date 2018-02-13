import { config } from '_/config/config';

import * as types from '_/vuex/mutation-types';

import _ from 'lodash';

import { api } from '_/services/api';

import { apiResponseCommit, errorCommit } from '_/vuex/actions/commonActions';

import { pError } from '_/helpers/logger';

import { router } from '_/router/router';

import { store } from '_/vuex/store';

export default {
    /**
     * Bootstraps the module.
     *
     * @param   {function}  options.dispatch
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {integer}  options.menuId
     * @param   {integer}  options.menuItemId
     * @return  {promise}
     */
    bootstrap ({ dispatch, commit, state }, { menuId = null, menuItemId = null }) {
        commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: true });

        const uri = `${config.ENV.apiBasePath}/menus`;

        return api['get'](uri)
            .then((response) => {
                dispatch('storeAllModules', { allMenus: response.body.body.menus });

                // If menuId is not set, reroute to first menu
                if (!menuId) {
                    const firstMenu = _.head(state.allMenus);

                    router.push(`/menu-items-editor/${firstMenu.id}`);

                    commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });

                    return;
                }

                const selectedMenu = _.find(state.allMenus, { 'id': parseInt(menuId) });

                // If invalid menuId is provided, reroute to 404 page
                if(!selectedMenu) {
                    pError('Unable to find a menu with provided menuId', menuId);

                    router.push('/error/resource-not-found');

                    commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });

                    return;
                }

                dispatch('updateSelectedMenu', { selectedMenu: selectedMenu });

                if (menuItemId) {
                    return dispatch('getEntry', { menuItemId })
                        .then(() => {
                            commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });


                            return true;
                        })
                        .catch((error) => {
                            pError('menuItemsEditor/getEntry action failed with an error', error);

                            router.push('/error/resource-not-found');

                            commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });

                        });
                }

                dispatch('setCreateEntryUI');

                commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });

                return true;
            })
            .catch((response) => {
                pError('Failed to load values for menu picker select2 component.', response);

                commit('MENU_ITEMS_EDITOR_BOOTSTRAP_IN_PROGRESS', { value: false });
            });

    },

    /**
     * Clears the UI from errors
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearAdminErrors ({ commit }) {
        commit('CLEAR_MENU_ITEMS_EDITOR_ERRORS');
    },

    /**
     * Commits a CONFIRM_MENU_DELETE which switches the ui to delete confirmation mode
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    confirmDeleteEntry ({ commit }) {
        commit(types.CONFIRM_MENU_ITEM_DELETE);
    },

    /**
     * Commits a CREATE_ANOTHER_MENU_ITEM which toggles the redirection to created entry/new entry
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    createAnother ({ commit }, { value }) {
        commit(types.CREATE_ANOTHER_MENU_ITEM, { value });
    },

    /**
     * Deletes a menu from API
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {promise}
     */
    deleteEntry ({ commit, dispatch, state }, { entryId }) {
        if (!state.confirmDeleteEntry) {
            return;
        }

        dispatch('setSubmitEntryInProgress', { value: true });

        return api.delete('menus/items/' + entryId)
            .then(() => {
                router.push(`/menu-items-editor/${state.selectedMenu.id}`);

                dispatch('setCreateEntryUI');

                dispatch('setSubmitEntryInProgress', { value: false });
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MENU_ITEMS_EDITOR');
            });
    },

    /**
     * Gets a single entry by entry id.
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {integer}  options.menuId
     * @param   {integer}  options.menuItemsId
     * @return  {promise}
     */
    getEntry ({ commit, dispatch, state }, { menuItemId }) {
        return api.get(`menus/items/${menuItemId}`)
            .then((response) => {
                apiResponseCommit({ commit }, response);

                dispatch('setMenuItemsEditorUi');
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MENU_ITEMS_EDITOR');
            });
    },

    /**
     * Commits MENU_ITEMS_EDITOR_SET_CREATE_ENTRY_UI mutation to setup the UI for new entry creation (empty form)
     *
     * @param   {function}  options.commit
     * @param   {object}  options.dispatch
     * @return  {void}
     */
    setCreateEntryUI ({ commit, dispatch }) {
        commit('MENU_ITEMS_EDITOR_SET_CREATE_ENTRY_UI');

        dispatch('setMenuItemsEditorUi');
    },

    /**
     * Sets up UI for the menu editor section (no arguments required, sets it by checking current state objects)
     *
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {void}
     */
    setMenuItemsEditorUi ({ state }) {
        store.dispatch('ui/setTitleSubtext', 'Menu items editor is used to create and edit Photon CMS menu items.');

        store.dispatch('ui/setTitleIcon', 'fa fa-list');

        if (state.editorMode === 'create') {
            store.dispatch('ui/setTitle', `Creating a new menu item under '${state.selectedMenu.title}' menu`);

            store.dispatch('ui/setBreadcrumbItems', [{
                link: '/menu-items-editor',
                text: 'Menu Items Editor',
            }]);

            return;
        }

        // Else if in edit mode set title to editing menu + corresponding menu title
        store.dispatch('ui/setTitle', `Editing menu item: ${state.editedEntry.title}`);

        /**
         * Set breadcrumb to two levels with hard coded name and corresponding menu editor as first,
         * and edited menu name and corresponding menu link as second level
         */
        store.dispatch('ui/setBreadcrumbItems', [{
            link: '/menu-items-editor',
            text: 'Menu Editor',
        }, {
            link: `/menu-items-editor/${state.editedEntry.name}`,
            text: state.editedEntry.title,
        }]);
    },

    /**
     * Sets the submitInProgress state property
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    setSubmitEntryInProgress ({ commit }, { value }) {
        commit('MENU_ITEMS_EDITOR_SUBMIT_IN_PROGRESS', { value });
    },

    /**
     * Store all modules data
     *
     * @param   {function}  options.commit
     * @param   {object}  options.allMenus
     * @return  {void}
     */
    storeAllModules ({ commit }, { allMenus }) {
        commit('LOAD_ALL_MENUS_SUCCESS', { allMenus });
    },

    /**
     * Submits (POST or PUT) entry to API
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {promise}
     */
    submitEntry ({ commit, dispatch, state }) {
        let action = 'post';

        let uri = 'menus/items';

        let payload = Object.assign({}, store.state.menuItemsEditor.editedEntry);

        const payloadPromise = getPayload(payload);

        return payloadPromise.then((payload) => {
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
                        if(!state.createAnother) {
                            router.push(`/menu-items-editor/${state.selectedMenu.id}/${response.data.body.menu_item.id}`);
                        } else {
                            dispatch('setCreateEntryUI');
                        }
                    } else {
                        dispatch('setMenuItemsEditorUi');
                    }

                    return response.data.body;
                })
                .catch((response) => {
                    errorCommit({ commit }, response, 'MENU_ITEMS_EDITOR');
                });
        });
    },

    /**
     * Empties the editedEntry.adminEntry state property
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    unsetAdminEntryField({ commit }) {
        commit(types.UNSET_MENU_ITEM_ADMIN_ENTRY);
    },

    /**
     * Updates the editedEntry record
     *
     * @param   {function}  options.commit
     * @param  {string}  options.name
     * @param  {mixed}  options.newValue
     * @return  {void}
     */
    updateMenuField ({ commit }, { name, newValue }) {
        commit('UPDATE_MENU_ITEM_FIELD', { name, newValue });
    },

    /**
     * Commits the selected node data to the state object
     *
     * @param   {function}  options.commit
     * @param   {string}  options.anchorText
     * @param   {string}  options.url
     * @return  {void}
     */
    updateSelectedNode({ commit }, { anchorText, url }) {
        commit(types.UPDATE_MENU_ITEM_SELECTED_NODE, { anchorText, url });
    },

    /**
     * Commits the selected menu id to the state object
     *
     * @param   {function}  options.commit
     * @param   {object}  options.selectedMenu
     * @return  {void}
     */
    updateSelectedMenu({ commit }, { selectedMenu }) {
        commit(types.UPDATE_SELECTED_MENU, { selectedMenu });
    },
};

/**
 * Transforms the state.editedEntry data to use with POST/PUT /api/menus/items request
 *
 * @param   {object}  state
 * @return  {object}
 */
function getPayload (payload) {
    // Handle the admin_panel_module_link menu link type
    if (parseInt(payload.menu_link_type_id) === 1) {
        delete payload.icon;

        const adminModule =  _.find(store.state.photonModule.modules, { 'id': parseInt(payload.adminModule) });

        payload.resource_data = JSON.stringify(adminModule);
    }

    // Handle the static_link menu link type
    if (parseInt(payload.menu_link_type_id) === 2) {
        payload.resource_data = payload.staticLink;
    }

    // Handle the menu_item_group menu link type
    if (parseInt(payload.menu_link_type_id) === 3) {
        delete payload.slug;
    }

    // Handle the admin_panel_single_entry menu link type
    if (parseInt(payload.menu_link_type_id) === 4) {
        delete payload.icon;

        const adminModule =  _.find(store.state.photonModule.modules, { 'id': parseInt(payload.adminModule) });

        payload.resource_data = JSON.stringify(adminModule);

        const uri = `${adminModule.table_name}/${payload.adminEntry}`;

        return api['get'](uri)
            .then((response) => {
                let entryData = {};

                entryData['id'] = response.body.body.entry.id;

                entryData['anchor_text'] = response.body.body.entry.anchor_text;

                entryData['anchor_html'] = response.body.body.entry.anchor_html;

                payload.entry_data = JSON.stringify(entryData);

                return cleanUpPayload(payload);
            })
            .catch((response) => {
                pError('Failed to get the payload.entry_data from the API.', response);
            });
    }

    /**
     * Return a promise for all menu_link_type_id other than === '4' to keep the return value consistent
     */
    return new Promise((resolve) => {
        resolve(payload);
    });
}

/**
 * Cleans up the payload before POST or PUT action is performed
 *
 * @param   {object}  payload
 * @return  {object}
 */
function cleanUpPayload (payload) {
    delete payload.adminEntry;

    delete payload.adminModule;

    delete payload.staticLink;

    return payload;
}
