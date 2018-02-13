import { api } from '_/services/api';

import {
    apiResponseCommit,
    errorCommit,
} from '_/vuex/actions/commonActions';

import { store } from '_/vuex/store';

import * as types from '_/vuex/mutation-types';

import i18n from '_/i18n';

export default {
    /**
     * Clears generator UI errors
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearGeneratorErrors ({ commit }) {
        commit(types.CLEAR_GENERATOR_ERRORS);
    },

    /**
     * Commits cleanup message
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    clearReport ({ commit }) {
        commit(types.CLEAR_MODULE_CREATION_REPORT);
    },

    /**
     * Commits CREATE_GENERATOR_SELECTED_MODULE_FIELD mutation
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    createNewField ({ commit }) {
        commit(types.CREATE_GENERATOR_SELECTED_MODULE_FIELD);
    },

    /**
     * Commits fieldToDelete (full module field object) with DELETE_GENERATOR_SELECTED_MODULE_FIELD mutation
     *
     * @param   {function}  options.commit
     * @param   {object}  options.fieldToDelete
     * @return  {void}
     */
    deleteField ({ commit }, { fieldToDelete }) {
        commit(types.DELETE_GENERATOR_SELECTED_MODULE_FIELD, fieldToDelete);
    },

    /**
     * If reporting is true, commits the MODULE_DELETE_REPORT_SUCCESS mutation
     * which displays reporting dialog where you can confim deleting module
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {boolean}  options.reporting
     * @return  {promise}
     */
    deleteModule ({ commit, state }, { reporting }) {
        if (reporting) {
            commit(types.MODULE_DELETE_REPORT_SUCCESS);

            // TODO: this should return a promise for consistency
            return;
        }

        // Extracts module table name from generator store
        const module = state.selectedModule.table_name;

        return api.delete('modules/' + module)
            .then((response) => {
                apiResponseCommit({ commit }, response, 'GENERATOR');

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'GENERATOR');
            });
    },

    /**
     * Cleans up the generator UI
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    generatorCleanup ({ commit }) {
        commit(types.GENERATOR_CLEANUP);
    },

    /**
     * Populates nodes state array
     *
     * @param   {object}  options.modules
     * @param   {function}  options.commit
     * @return  {void}
     */
    populateNodes ({ commit }, { modules }) {
        commit(types.POPULATE_NODES, { modules });
    },

    /**
     * Selects a module for generator manipulation
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.moduleTableName
     * @return  {promise}
     */
    selectGeneratorModule ({ commit, dispatch, state }, { moduleTableName }) {
        commit(types.CLEAR_GENERATOR_ERRORS);

        if (!moduleTableName) {
            return new Promise(function(resolve) {
                commit(types.UPDATE_GENERATOR_SELECTED_MODULE, false);

                resolve();
            });
        }

        return store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName, refreshModule: true })
            .then(() => {
                return dispatch('generatorCleanup').
                    then(() => {
                        if (!store.state.photonModule.moduleInformation[moduleTableName]) {
                            commit(types.UPDATE_GENERATOR_SELECTED_MODULE, false);

                            return;
                        }

                        const selectedModuleCopy = Object.assign({}, store.state.photonModule.moduleInformation[moduleTableName]);

                        commit(types.UPDATE_GENERATOR_SELECTED_MODULE, selectedModuleCopy);
                    });
            });
    },

    /**
     * Handles title, subtitle, text, icon for generator,
     * no arguments required should be called after any state change which requires updating the UI
     *
     * @param   {object}  options.state
     * @return  {void}
     */
    setGeneratorUi ({ state }) {
        store.dispatch(
            'ui/setTitleSubtext',
            i18n.t('generator.titleSubText', { 'companyTitle': store.state.ui.photonConfig.companyTitle })
        );

        // If new module is created
        if (state.newModule) {
            store.dispatch('ui/setTitle', i18n.t('generator.creatingANewModule'));

            store.dispatch('ui/setTitleIcon', 'fa fa-gear');

            store.dispatch('ui/setBreadcrumbItems', [{
                text: i18n.t('generator.generator'),
                link: '/generator',
            }]);

            return;
        }

        // If manipulating an existing module
        store.dispatch('ui/setTitle', 'Editing module: ' + state.selectedModule.name);

        store.dispatch('ui/setTitleIcon', state.selectedModule.icon);

        store.dispatch('ui/setBreadcrumbItems', [{
            text: i18n.t('generator.generator'),
            link: '/generator',
        }, {
            text: state.selectedModule.name.toUpperCase(),
            link: '/generator/' + state.selectedModule.table_name,
        }]);
    },


    /**
     * POST or PUT selected module to API route
     *
     * @param   {function}  options.commit
     * @param   {object}  options.state
     * @param   {boolean}  options.reporting
     * @return  {promise}
     */
    submitModuleForm ({ commit, state }, { reporting }) {
        let action = 'post';

        // Default URI is just 'modules' (no module for POST)
        let uri = 'modules';

        // Create a copy of selected module for payload manipulation
        let modulePayload = JSON.parse(JSON.stringify(state.selectedModule));

        // If not creating a new module
        if (!state.newModule) {
            // Action becomes PUT
            action = 'put';

            // URI is updated to include module being manipulated
            uri = uri + '/' + modulePayload.table_name;
        }

        // If module has catogory set to 0, which is no category
        if (modulePayload.category === 0) {
            // Delete category field from modulePayload
            delete modulePayload.category;
        }

        for (var i = 0; i < modulePayload.fields.length; i++) {
            if (modulePayload.fields[i].newField) {
                delete modulePayload.fields[i].id;

                delete modulePayload.fields[i].newField;
            }
        }

        // Create API request payload object
        const payload = {
            fields: modulePayload.fields,
            module: modulePayload,
            reporting,
        };

        // Remove excess fields objects from payload.module (already included as payload.fields)
        delete payload.module.fields;

        return api[action](uri, payload)
            .then((response) => {
                apiResponseCommit({ commit }, response, 'GENERATOR');

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'GENERATOR');
            });
    },

    /**
     * Updates the refreshForm state property
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.value
     * @return  {void}
     */
    updateRefreshForm ({ commit }, { value }) {
        commit(types.SET_GENERATOR_REFRESH_FORM, { value });
    },

    /**
     * Commits field position change
     *
     * @param   {function}  options.commit
     * @param   {array}  options.newOrder
     * @return  {void}
     */
    updateFieldsOrder ({ commit }, { newOrder }) {
        commit(types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_ORDER, { newOrder });
    },

    /**
     * Updates the field property
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.id
     * @param   {any}  options.newValue
     * @return  {void}
     */
    updateFieldProperty ({ commit }, { id, newValue }) {
        commit(types.UPDATE_GENERATOR_FIELD_PROPERTY, { id, newValue });
    },
};
