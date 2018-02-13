import * as types from '_/vuex/mutation-types';

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
     * @param   {integer}  options.menuId
     * @return  {promise}
     */
    bootstrap ({ dispatch }, { menuId = null }) {
        if (menuId) {
            return dispatch('getEntry', { menuId })
                .then(() => {
                    return true;
                })
                .catch((error) => {
                    pError('menuEditor/getEntry action failed with an error', error);

                    router.push('/error/resource-not-found');
                });
        }

        // else if no menuId provided
        dispatch('setCreateEntryUI');

        /**
         * Return a promise to keep the return value consistent
         */
        return new Promise((resolve) => {
            resolve(true);
        });
    },

    /**
     * Clears the UI from errors
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearAdminErrors ({ commit }) {
        commit('CLEAR_MENU_EDITOR_ERRORS');
    },

    /**
     * Commits a CONFIRM_MENU_DELETE which switches the ui to delete confirmation mode
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    confirmDeleteEntry ({ commit }) {
        commit(types.CONFIRM_MENU_DELETE);
    },

    /**
     * Commits a CREATE_ANOTHER_MENU which toggles the redirection to created entry/new entry
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    createAnother ({ commit }, { value }) {
        commit(types.CREATE_ANOTHER_MENU, { value });
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

        return api.delete('menus/' + entryId)
            .then(() => {
                router.push('/menu-editor');

                dispatch('setCreateEntryUI');

                dispatch('setSubmitEntryInProgress', { value: false });
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MENU_EDITOR');
            });
    },

    /**
     * Gets a single entry by entry id.
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.menuId
     * @return  {promise}
     */
    getEntry ({ commit, dispatch, state }, { menuId }) {
        return api.get('menus/' + menuId)
            .then((response) => {
                apiResponseCommit({ commit }, response);

                dispatch('setMenuEditorUi');
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MENU_EDITOR');
            });
    },

    /**
     * Commits MENU_EDITOR_SET_CREATE_ENTRY_UI mutation to setup the UI for new entry creation (empty form)
     *
     * @param   {function}  options.commit
     * @param   {object}  options.dispatch
     * @return  {void}
     */
    setCreateEntryUI ({ commit, dispatch }) {
        commit('MENU_EDITOR_SET_CREATE_ENTRY_UI');

        dispatch('setMenuEditorUi');
    },

    /**
     * Sets up UI for the menu editor section (no arguments required, sets it by checking current state objects)
     *
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {void}
     */
    setMenuEditorUi ({ state }) {
        store.dispatch('ui/setTitleSubtext', 'Menu editor is used to create and edit Photon CMS menus.');

        store.dispatch('ui/setTitleIcon', 'fa fa-list');

        if (state.editorMode === 'create') {
            store.dispatch('ui/setTitle', 'Creating a new menu');

            store.dispatch('ui/setBreadcrumbItems', [{
                link: '/menu-editor',
                text: 'Menu Editor',
            }]);

            return;
        }

        // Else if in edit mode set title to editing menu + corresponding menu title
        store.dispatch('ui/setTitle', `Editing menu: ${state.editedEntry.title}`);

        /**
         * Set breadcrumb to two levels with hard coded name and corresponding menu editor as first,
         * and edited menu name and corresponding menu link as second level
         */
        store.dispatch('ui/setBreadcrumbItems', [{
            link: '/menu-editor',
            text: 'Menu Editor',
        }, {
            link: `/menu-editor/${state.editedEntry.name}`,
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
        commit('MENU_EDITOR_SUBMIT_IN_PROGRESS', { value });
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

        let uri = 'menus';

        // Makes a copy of the menu so the state object doesn't get mutated
        const payload = Object.assign({}, state.editedEntry);

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
                        router.push(`/menu-editor/${response.data.body.menu.id}`);
                    } else {
                        dispatch('setCreateEntryUI');
                    }
                } else {
                    dispatch('setMenuEditorUi');
                }

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MENU_EDITOR');
            });
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
        commit('UPDATE_MENU_FIELD', { name, newValue });
    },
};
