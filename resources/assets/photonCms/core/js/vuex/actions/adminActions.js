import _ from 'lodash';

import { api } from '_/services/api';

import { store } from '_/vuex/store';

import * as types from '_/vuex/mutation-types';

import i18n from '_/i18n';

import { runPreSubmitAction } from '~/vuex/actions/preSubmitActions';

import { eventBus } from '_/helpers/eventBus';

import { router } from '_/router/router';

import {
    apiResponseCommit,
    errorCommit
} from '_/vuex/actions/commonActions';

import { pError } from '_/helpers/logger';

const photonConfig = require('~/config/config.json');

export default {
    /**
     * If the module type is single_entry, checks if first record exists or not.
     *
     * @param   {object}  options.state
     * @param   {string}  options.moduleTableName
     * @return  {promise}
     */
    shouldRerouteToFirstEntry ({ state }, { moduleTableName }) {
        if (state.selectedModule.type != 'single_entry') {
            return Promise.reject();
        }
        return api.get(moduleTableName + '/1')
            .then((response) => {
                return response;
            });
    },

    /**
     * Bootstrap the module admin panel view
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.moduleTableName
     * @param   {integer}  options.moduleEntryId
     * @param   {boolean}  options.shouldFetchSearch
     * @return  {void}
     */
    adminBootstrap ({ commit, dispatch, state }, { moduleTableName, moduleEntryId, shouldFetchSearch }) {
        return new Promise((resolve, reject) => {
            // Getting modules data and specific module data is required before anything else
            Promise.all([
                dispatch('getCurrentlyEditedModule', { moduleTableName }),
            ]).then(() => {
                if (!state.selectedModule) {
                    return reject(`Module ${moduleTableName} doesn't exist.`);
                }

                // If module entry ID wasn't provided
                if (!moduleEntryId) {
                    return dispatch('shouldRerouteToFirstEntry', { moduleTableName })
                        .then(() => {
                            // If a module type is single_entry, and first record exists, reroute
                            router.push(`/admin/${moduleTableName}/1`);

                            return resolve();
                        }, () => {
                            // else allow new entry creation
                            dispatch('createEntry');

                            if (shouldFetchSearch) {
                                const payload = store.state.advancedSearch.payload;

                                store.dispatch('advancedSearch/filterModuleEntries', { moduleTableName, payload });

                                dispatch('changeEditorMode', { newEditorMode: 'search' });
                            }

                            eventBus.$emit('adminBootstrapCreateEntry', { moduleTableName });

                            return resolve();
                        });
                }

                // Else if module entry ID was provided
                const getEditedModuleEntryPromise = dispatch(
                    'getModuleEntry', {
                        id: moduleEntryId,
                        moduleTableName,
                    });

                getEditedModuleEntryPromise
                    .then(() => {
                        const entry = state.entry
                            ? state.entry
                            : null;

                        if (!entry) {
                            return reject(`Entry ${moduleTableName}.${moduleEntryId} doesn't exist.`);
                        }

                        // If entry exists, make it a currently edited entry
                        dispatch('updateEditedEntry');

                        // Setup the UI
                        dispatch('setAdminUi');

                        store.dispatch('subscription/subscribe', { moduleTableName, entryId: moduleEntryId });

                        eventBus.$emit('adminBootstrapEditEntry', { moduleTableName });

                        // Promise is resolved (routing can complete)
                        return resolve();
                    })
                    .catch((error) => {
                        return reject(error);
                    });
            })
            .catch((error) => {
                // Promise is rejected on any error (rerouting to an error page)
                return reject(error);
            });
        });
    },

    /**
     * Changes admin mode
     *
     * @param   {function}  options.commit
     * @param   {string}  options.newEditorMode
     * @return  {void}
     */
    changeEditorMode ({ commit }, { newEditorMode }) {
        commit(types.CHANGE_ADMIN_MODE, { newEditorMode });
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
     * Commits a CREATE_ANOTHER which toggles the redirection to created entry/new entry
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    createAnother ({ commit }, { value }) {
        commit(types.CREATE_ANOTHER, { value });
    },

    /**
     * Creates a new edited entry store object from store template
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {integer}  options.parentId
     * @param   {integer}  options.scopeId
     * @return  {void}
     */
    createEntry({ commit, dispatch, state }) {
        if (!state.selectedModule.permission_control.crud.create) {
            return;
        }

        commit('SET_CREATE_ENTRY_UI');

        dispatch('setAdminUi');
    },

    /**
     * Gets the default search choice field name
     *
     * @param   {function}  options.commit
     * @param   {string}  options.selectedModule
     * @return  {object}
     */
    getDefaultSearchChoice({ commit }, { selectedModule }) {
        return api.get(`modules/${selectedModule}`)
            .then((getResponse) => {
                if(_.findIndex(getResponse, 'data.body.module.fields')) {
                    const field = _.find(getResponse.data.body.module.fields, { is_default_search_choice: true });

                    if (!_.isEmpty(field)) {
                        return field;
                    }
                }
            })
            .catch((getResponse) => {
                pError('Failed to fetch default search choice info.', getResponse);

                return Promise.reject(getResponse);
            });
    },

    /**
     * Creates the new search choice (used in Select2 component)
     *
     * @param   {function}  options.commit
     * @param   {string}  options.selectedModule
     * @param   {string}  options.term
     * @return  {object}
     */
    createSearchChoice({ commit }, { selectedModule, term }) {
        const payload = {};

        /**
         * Get the related module data to fetch the field with 'is_default_search_choice' set to true
         */
        return api.get(`modules/${selectedModule}`)
            .then((getResponse) => {
                if(_.findIndex(getResponse, 'data.body.module.fields')) {
                    const field = _.find(getResponse.data.body.module.fields, { is_default_search_choice: true });

                    if (!_.isEmpty(field)) {
                        const searchFilter = {};

                        searchFilter[field.column_name] = {
                            equal: term,
                        };

                        // Try to find an existing entry
                        const searchPayload = {
                            filter: searchFilter,
                            include_relations: false,
                        };

                        return api.post(`filter/${selectedModule}`, searchPayload)
                            .then((response) => {
                                const values = response.data.body.entries;
                                // If entry exists return the data
                                if (!_.isEmpty(values)) {
                                    const existingEntry = values.shift();

                                    return Promise.resolve({
                                        id: existingEntry.id,
                                        text: existingEntry.anchor_text,
                                    });
                                }
                                // Else insert new entry and return new data
                                payload[field.column_name] = term;

                                return api.post(selectedModule, payload)
                                    .then((postResponse) => {
                                        return Promise.resolve({
                                            id: postResponse.data.body.entry.id,
                                            text: postResponse.data.body.entry.anchor_text,
                                        });
                                    })
                                    .catch((postResponse) => {
                                        pError('Failed creating a search choice', postResponse);

                                        return Promise.reject(postResponse);
                                    });
                            }, () => {
                                pError('Failed to check if entry exists.', getResponse);

                                return Promise.reject();
                            });
                    }
                }

                pError('Failed creating a search choice.', 'No is_default_search_choice fields found.', getResponse);

                return Promise.reject();
            })
            .catch((getResponse) => {
                pError('Failed to fetch related module data.', getResponse);

                return Promise.reject(getResponse);
            });
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
     * @param   {object}  options.state
     * @return  {promise}
     */
    deleteEntry ({ commit, dispatch, state }, { entryId, selectedModuleName }) {
        if (!state.confirmDeleteEntry) {
            return Promise.reject('Aborting deletion, state.confirmDeleteEntry is set to false.');
        }

        dispatch('setSubmitEntryInProgress', { value: true });

        return api.delete(`${selectedModuleName}/${entryId}`)
            .then((response) => {
                dispatch('changeEditorMode', { newEditorMode: 'create' });

                commit(types.DELETE_DYNAMIC_MODULE_ENTRY_SUCCESS);

                dispatch('setSubmitEntryInProgress', { value: false });

                return Promise.resolve(response);
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'ADMIN');

                dispatch('setSubmitEntryInProgress', { value: false });

                return Promise.reject(response);
            });
    },

    /**
     * Commits a DISABLE_AUTOMATIC_SLUG_GENERATION which disables the automatic slug updates
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    disableAutomaticSlugGeneration ({ commit }, { value }) {
        commit(types.DISABLE_AUTOMATIC_SLUG_GENERATION, { value });
    },

    /**
     * Empties the lazyLoadedNodes state property
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    emptyLazyLoadedNodes ({ commit }) {
        commit('EMPTY_LAZY_LOADED_NODES');
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
     * Custom functionallity introduced for invitations module, a URL is received
     * from the API to call a certain custom action on the entry
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.url
     * @return  {promise}
     */
    extensionCall ({ commit, dispatch, state }, { url }) {
        /**
         * First, the url is trimmed to exclude '/api/' prefix since all API calls are already
         * prefixed on the API service level
         */
        url = url.replace('/api/', '');

        return api.get(url)
            .then(() => {
                return dispatch('getModuleEntry', {
                    id: state.editedEntry.id,
                    moduleTableName: state.selectedModule.table_name,
                }).then((entry) => {
                    /**
                     * After updating the entry mark it as edited by calling the updateEditedEntry
                     * action (defined in this file)
                     */
                    dispatch('updateEditedEntry', {
                        entry,
                        moduleTableName: state.selectedModule.table_name,
                    });
                });
            })
            .catch((response) => {
                return errorCommit({ commit }, response, 'ADMIN');
            });
    },

    /**
     * Gets the currently edited module
     *
     * @param   {string}  moduleTableName
     * @return  {promise}
     */
    getCurrentlyEditedModule({ commit, state }, { moduleTableName }) {
        return store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName })
            .then(() => {
                /**
                 * Can't Promise catch() here since API handler would have already caught if
                 * a module does not exist. So this just checks the store to see if it's there
                 */
                if (!store.state.photonModule.moduleInformation[moduleTableName]) {
                    commit(types.UPDATE_ADMIN_SELECTED_MODULE, { selectedModule: {} });

                    return;
                }

                /**
                 * Makes a copy of the module so the photonModule state doesn't get mutated
                 * by admin edits (it should only be updated from API).
                 */
                const selectedModuleCopy = Object.assign(
                    {},
                    store.state.photonModule.moduleInformation[moduleTableName]
                );

                commit(types.UPDATE_ADMIN_SELECTED_MODULE, { selectedModule: selectedModuleCopy });
            });
    },

    /**
     * Gets a single module entry by moduleTableName and id. Takes a refreshEntry argument
     * which forces data pull from API, instead of getting it from state
     * if it was already cached there.
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {integer}  options.id
     * @param   {string}  options.moduleTableName
     * @return  {promise}
     */
    getModuleEntry({ commit, state } , { id, moduleTableName }) {
        commit(types.CLEAR_ADMIN_ERRORS);

        return api.get(moduleTableName + '/' + id)
            .then((response) => {
                commit(types.LOAD_DYNAMIC_MODULE_ENTRY_SUCCESS, { entry: response.data.body.entry });

                return state.entry ? state.entry : null;
            })
            .catch((response) => {
                return errorCommit({commit}, response, 'ADMIN');
            });
    },

    /**
     * Emits the event upon hitting bottom of an assets container
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {string}  options.tableName
     * @return  {void}
     */
    loadPaginatedNodes({ state, commit }, { tableName }) {
        const lazyLoadedNodesLength = state.lazyLoadedNodes.length > 0 ? state.lazyLoadedNodes.length : 0;

        const itemsPerPage = photonConfig.paginatedNodesItemsPerPage;

        const payload = {
            include_relations: false,
            pagination: {
                current_page: Math.ceil(lazyLoadedNodesLength / itemsPerPage) + 1,
                items_per_page: itemsPerPage,
            },
            sorting: {
                updated_at: 'desc',
            }
        };

        return api.post(`filter/${tableName}`, payload)
            .then((response) => {
                const values = response.data.body.entries;

                commit(types.CONCAT_LAZY_LOADED_NODES, { values });

                if(response.data.body.pagination.last_page <= payload.pagination.current_page) {
                    return Promise.reject(values);
                }

                return Promise.resolve(values);
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Performs a mass edit action
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {promise}
     */
    massEdit ({ commit, dispatch, state }) {
        const selectedModuleName = state.selectedModule.table_name;

        const action = 'put';

        let uri = selectedModuleName;

        // Makes a copy of the entry so the state object doesn't get mutated
        const payload = Object.assign({}, state.editedEntry);

        // Clean up PUT payload to only include edited fields
        for (const key in payload) {
            if (payload[key] === null || payload[key] === undefined) {
                delete payload[key];
            }
        }

        // Makes a copy of the filter so the state object doesn't get mutated
        payload.filter = Object.assign({}, store.state.advancedSearch.payload.filter);

        return api[action](uri, payload)
            .then((response) => {
                apiResponseCommit({ commit }, response);

                if (response.data.message === 'MASS_UPDATE_DYNAMIC_MODULE_ENTRY_SUCCESS') {
                    const notification = {
                        compiled_message: `${response.data.body.updated_entries.length} entries were updated. (${response.data.body.failed_entries.length} entries failed to update).`,
                        subject: 'Sucessful mass update action',
                    };
                    // Notify a user of the outcome
                    store.dispatch('notification/addNotifications', {
                        notifications: [notification],
                    });

                    // Reload the search results
                    dispatch('filterModuleEntries', {
                        moduleTableName: selectedModuleName,
                        payload: state.advancedSearch,
                    });
                }

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'ADMIN');
            });
    },

    /**
     * Sets up UI for the admin section (no arguments required,
     * sets it by checking the state object)
     *
     * @param   {object}  options.state
     * @return  {void}
     */
    setAdminUi ({ state }) {
        // Hard coded title subtext is always applied
        store.dispatch('ui/setTitleSubtext', state.selectedModule.name);

        // Icon is always applied from selected/edited module icon
        store.dispatch('ui/setTitleIcon', state.selectedModule.icon);

        if (!state.editedEntry) {
            // If no entry is being edited set title to selected/edited module's name
            // store.dispatch('ui/setTitle', state.selectedModule.name);

            // Set breadcrumb to single level with selected/edited module's name and admin link
            store.dispatch('ui/setBreadcrumbItems', [{
                text: state.selectedModule.name.toUpperCase(),
                link: `/admin/${state.selectedModule.table_name}`
            }]);

            return;
        }

        if (state.editorMode === 'edit') {
            store.dispatch('ui/setTitle', 'Editing: ' + state.editedEntry.anchor_text);

            store.dispatch('ui/setBreadcrumbItems', [{
                text: state.selectedModule.name.toUpperCase(),
                link: `/admin/${state.selectedModule.table_name}`,
            }, {
                text: state.editedEntry.anchor_text.toUpperCase(),
                link: `/admin/${state.selectedModule.table_name}/${state.editedEntry.id}`,
            }]);

            return;
        }

        if (state.editorMode === 'search') {
            store.dispatch('ui/setBreadcrumbItems', [{
                text: state.selectedModule.name.toUpperCase(),
                link: `/admin/${state.selectedModule.table_name}`,
            }, {
                text: state.selectedModule.name.toUpperCase() + ' ' + i18n.t('admin.advancedSearch'),
                link: `/admin/${state.selectedModule.table_name}`,
            }]);

            return;
        }

        store.dispatch('ui/setTitle', i18n.t('admin.creatingANewEntry'));

        store.dispatch('ui/setBreadcrumbItems', [{
            text: state.selectedModule.name.toUpperCase(),
            link: `/admin/${state.selectedModule.table_name}`,
        }, {
            text: `${i18n.t('admin.new')} '${state.selectedModule.name.toUpperCase()}' ${i18n.t('admin.entry')}`,
            link: `/admin/${state.selectedModule.table_name}`,
        }]);
    },

    /**
     * Sets the assetManagerSearchFilter object
     *
     * @param  {function}  options.commit
     * @param  {object}  options.value
     * @return  {void}
     */
    setAssetManagerSearchFilterObject({ commit }, { value }) {
        commit(types.SET_ASSET_MANAGER_SEARCH_FILTER, { value });
    },

    /**
     * Commits MENU_EDITOR_SET_CREATE_ENTRY_UI mutation to setup the UI for new entry creation (empty form)
     *
     * @param   {function}  options.commit
     * @param   {object}  options.dispatch
     * @return  {void}
     */
    setCreateEntryUI ({ commit, dispatch }) {
        commit('SET_CREATE_ENTRY_UI');

        dispatch('setAdminUi');
    },

    /**
     * Sets the submitInProgress state property
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    setSubmitEntryInProgress ({ commit }, { value }) {
        commit('SUBMIT_IN_PROGRESS', { value });
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
        return runPreSubmitAction()
            .then(() => {
                const selectedModuleName = state.selectedModule.table_name;

                let action = 'post';

                let uri = selectedModuleName;

                // Makes a copy of the entry so the state object doesn't get mutated
                let payload = Object.assign({}, state.editedEntry);

                // Handle the asset manager special case, as it needs to use multipart/form-data
                if (state.editorMode === 'create' && selectedModuleName === 'assets') {
                    let formData = new FormData();

                    _.forEach(payload, function(value, key) {
                        formData.append(key, value);
                    });

                    payload = formData;
                }

                // Alert a user that a if he changed his own email address,
                // mail must be checked for email confirmation link
                if (state.editorMode === 'edit'
                    && selectedModuleName === 'users'
                    && payload.id === store.state.user.meta.id
                    && _.has(payload, 'email')
                    && payload.email !== store.state.user.meta.email
                    ) {

                    const notifications = [{
                        subject: i18n.t('admin.emailChangeRequested'),
                        compiled_message: i18n.t('admin.emailChangeRequestedBody', { newEmail: payload.email }),
                    }];

                    store.dispatch('notification/addNotifications', { notifications });
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
                            dispatch('setCreateEntryUI');
                        } else {
                            dispatch('setAdminUi');
                        }

                        return Promise.resolve(response.data.body);
                    })
                    .catch((response) => {
                        errorCommit({commit}, response, 'ADMIN');

                        dispatch('setSubmitEntryInProgress', { value: false });

                        return Promise.reject(response);
                    });
            });
    },

    /**
     * Toggles the entryUpdated state value to force Admin form fields refresh
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    toggleEntryUpdated ({ commit }) {
        commit('TOGGLE_ENTRY_UPDATED');
    },

    /**
     * Commits a UPDATE_ENTRY mutation using entry object and moduleTableName
     * to identify new edited entry
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    updateEditedEntry ({ commit }) {
        commit('UPDATE_ENTRY');
    },

    /**
     * Updates the editedEntry record
     *
     * @param   {function}  options.commit
     * @param  {string}  options.name
     * @param  {mixed}  options.newValue
     * @return  {void}
     */
    updateEntryField ({ commit }, { name, newValue }) {
        commit('UPDATE_MODULE_ENTRY_FIELD', { name, newValue });
    },

    /**
     * Commits the selected node data to the state object
     *
     * @param   {function}  options.commit
     * @param   {string}  options.anchorText
     * @param   {string}  options.url
     * @return  {void}
     */
    updateSelectedNode({ commit }, { anchorText, parentId, scopeId, url }) {
        commit(types.UPDATE_SELECTED_NODE, { anchorText, parentId, scopeId, url });
    },
};
