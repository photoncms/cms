import _ from 'lodash';

import { api } from '_/services/api';

import * as types from '_/vuex/mutation-types';

import { store } from '_/vuex/store';

export default {
    /**
     * Creates new widge
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {string}  options.type
     * @return  {promise}
     */
    createNewWidget ({ commit, dispatch }, { type }) {
        const payload = {
            heading: '',
            icon: 'fa fa-circle',
            meta_data: '',
            module: '',
            private: false,
            refresh_interval: '10000',
            theme: 'panel-primary',
            type,
        };

        return dispatch('submitEntry', { payload })
            .then((response) => {
                commit(types.CREATE_NEW_WIDGET, { widget: response.entry });

                return response;
            });
    },

    /**
     * Deletes a widget
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.entryId
     * @return  {promise}
     */
    deleteEntry ({ commit }, { entryId }) {
        return api.delete(`widgets/${entryId}`)
            .then((response) => {
                commit(types.DELETE_WIDGET, { widgetId: entryId });

                return Promise.resolve(response);
            })
            .catch((response) => {
                return Promise.reject(response);
            });
    },

    /**
     * Gets latest entries from a given module
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.itemsPerPage
     * @param   {string}  options.moduleTableName
     * @return  {promise}
     */
    getLatestEntries ({ commit }, { itemsPerPage, moduleTableName }) {
        let payload = {
            include_relations: true,
            pagination: {
                current_page: 1,
                items_per_page: itemsPerPage,
            },
            sorting: {
                updated_at: 'desc',
            },
        };

        return api.post(`filter/${moduleTableName}`, payload)
            .then((response) => {
                const data = response.data.body.entries;

                return Promise.resolve(data);
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Gets the widget module data
     *
     * @param   {function}  options.commit
     * @return  {promise}
     */
    getWidgetModuleFields({ commit }) {
        const moduleTableName = 'widgets';

        return store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName })
            .then(() => {
                const selectedModuleCopy = Object.assign(
                    {},
                    store.state.photonModule.moduleInformation[moduleTableName]
                );

                commit(types.UPDATE_WIDGET_MODULE_FIELDS, { fields: selectedModuleCopy.fields });
            });
    },

    /**
     * Gets the selected module data
     *
     * @param   {function}  options.commit
     * @return  {promise}
     */
    getSelectedModuleFields({ commit }, { moduleTableName }) {
        if(!moduleTableName) {
            return Promise.resolve([]);
        }

        return store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName })
            .then(() => {
                const selectedModuleCopy = Object.assign(
                    {},
                    store.state.photonModule.moduleInformation[moduleTableName]
                );

                return selectedModuleCopy.fields;
            });
    },

    /**
     * Retrieves an array of widgets.
     *
     * @param  {function}  options.commit
     * @param  {function}  options.dispatch
     * @return  {promise}
     */
    getWidgets({ commit, dispatch }) {
        const payload = {
            filter: {
                private: {
                    equal: 0,
                }
            },
            include_relations: false,
            sorting: {
                lft: 'asc',
            }
        };

        return api.post('filter/widgets', payload)
            .then((response) => {
                dispatch('getPrivateWidgets')
                    .then(() => {
                        const values = response.data.body.entries;

                        commit(types.GET_WIDGETS_SUCCESS, { values });

                        return Promise.resolve(values);
                    }, () => {
                        return Promise.reject();
                    });
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Gets private widgets
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    getPrivateWidgets({ commit }) {
        const payload = {
            filter: {
                private: {
                    equal: 1,
                },
                created_by: {
                    equal: store.state.user.meta.id,
                },
            },
            include_relations: false,
            sorting: {
                lft: 'asc',
            }
        };

        return api.post('filter/widgets', payload)
            .then((response) => {
                const values = response.data.body.entries;

                commit(types.GET_WIDGETS_SUCCESS, { values });

                return Promise.resolve(values);
            }, () => {
                return Promise.reject();
            });
    },

    /**
     * Resets Widget Model
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    resetWidgetModel ({ commit }) {
        commit(types.RESET_WIDGET_MODEL);
    },

    /**
     * Submits (POST or PUT) entry to API
     *
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @return  {promise}
     */
    submitEntry({ dispatch, state }, { payload }) {
        let action = 'post';

        let uri = 'widgets';

        if (_.has(payload, 'id')) {
            action = 'put';

            uri = `${uri}/${payload.id}`;
        }

        let metaData = {};

        // filter the payload to include only widgets module fields
        for (const key in payload) {
            if(!(_.find(state.widgetsModuleFields, { column_name: key }) || key === 'id')) {
                metaData[key] = payload[key];

                delete payload[key];
            }
        }

        payload['meta_data'] = JSON.stringify(metaData);

        return api[action](uri, payload)
            .then((response) => {
                return Promise.resolve(response.data.body);
            })
            .catch((response) => {
                return Promise.reject(response);
            });
    },

    /**
     * Updates a widget
     *
     * @param   {function}  options.commit
     * @param   {object}  options.widget
     * @return  {void}
     */
    updateWidget({ commit }, { widget }) {
        commit(types.UPDATE_WIDGET, { widget });
    }
};
