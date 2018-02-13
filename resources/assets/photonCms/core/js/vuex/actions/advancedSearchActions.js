import * as types from '_/vuex/mutation-types';

import { api } from '_/services/api';

import { errorCommit } from '_/vuex/actions/commonActions';

import { store } from '_/vuex/store';

import i18n from '_/i18n';

export default {
    /**
     * Clears the advanced search filters
     *
     * @param   {function}  options.commit
     * @return  {[type]}  [description]
     */
    clearAdvancedSearchFilters ({ commit }) {
        commit(types.CLEAR_ADVANCED_SEARCH_FILTER);

        commit(types.UPDATE_CURRENT_PAGE_NUMBER, { value: 1 });
    },

    /**
     * Initiates the file download action
     *
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.fileType
     * @param   {integer}  options.entryId
     * @return  {promise}
     */
    downloadFile ({ dispatch, state }, { fileType, entryId }) {
        const payload = {};

        payload.filter = Object.assign({}, state.admin.advancedSearch.filter);

        payload.action = 'store';

        payload.file_type = fileType;

        let uri = `export/${state.admin.selectedModule.table_name}`;

        if (entryId) {
            uri = `${uri}/${entryId}`;
        }

        return api.post(uri, payload)
            .then((response) => {
                if (response.data.body.file_name) {
                    window.location.href = response.data.body.file_name;
                }

                return response.data.body;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'ADMIN');
            });
    },

    /**
     * Filters module's entries. Takes a refreshEntry argument which forces data pull from API,
     * instead of getting it from state if it was already cached there.
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {string}  options.moduleTableName
     * @param   {object}  options.payload
     * @param   {boolean}  options.refreshEntries
     * @return  {promise}
     */
    filterModuleEntries ({ commit, dispatch, state }, { moduleTableName, payload } = {}) {
        return api.post('filter/' + moduleTableName, payload)
            .then((response) => {
                if (response.data.body.pagination !== 'undefined') {
                    const entries = response.data.body.entries;

                    commit(types.UPDATE_FILTERED_ENTRIES, { entries });

                    const pagination = response.data.body.pagination;

                    commit(types.LOAD_DYNAMIC_MODULE_ENTRIES_PAGINATION, { pagination });

                    dispatch('setSearchTitle');
                }

                // Hide mass editing form
                commit(types.SET_MASS_EDITOR_VISIBILITY, { value: false });
            })
            .catch((response) => {
                return errorCommit({ commit }, response, 'ADMIN');
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
     * Hides the mass editor UI
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    hideMassEditor ({ commit }) {
        commit(types.SET_MASS_EDITOR_VISIBILITY, false);
    },

    /**
     * Navigates to the specified result page
     *
     * @param   {function}  options.commit
     * @param   {function}  options.dispatch
     * @param   {object}  options.state
     * @param   {integer}  options.value
     * @return  {void}
     */
    navigateToPage ({ commit, dispatch, state }, { value }) {
        commit(types.UPDATE_CURRENT_PAGE_NUMBER, { value });

        const moduleTableName = store.state.admin.selectedModule.table_name;

        const payload = state.payload;

        return dispatch('filterModuleEntries', { moduleTableName, payload });
    },

    /**
     * Sets the advanced search filter object
     *
     * @param  {function}  options.commit
     * @param  {object}  options.value
     * @return  {void}
     */
    setAdvancedSearchFilterObject({ commit }, { value }) {
        commit(types.SET_ADVANCED_SEARCH_FILTER, { value });
    },

    /**
     * Sets a search title
     *
     * @param   {object}  options.state
     * @return  {void}
     */
    setSearchTitle ({ state }) {
        const currentPage = state.entriesPagination.current_page;

        const lastPage = state.entriesPagination.last_page;

        if (lastPage > 0) {
            store.dispatch(
                'ui/setTitle',
                i18n.t('admin.advancedSearchTitle', {
                    currentPage,
                    lastPage,
                    moduleName: store.state.admin.selectedModule.name,
                })
            );

            return;
        }

        store.dispatch('ui/setTitle', `No ${store.state.admin.selectedModule.name} entries found.`);
    },

    /**
     * Shows the mass editor UI
     *
     * @param   {function}  options.commit
     * @return  {void}
     */
    showMassEditor ({ commit }) {
        commit(types.SET_MASS_EDITOR_VISIBILITY, { value: true });
    },

    /**
     * Updates the filter value
     *
     * @param   {function}  options.commit
     * @param   {string}  options.fieldName Represents the filter path in the format 'filter.fieldName.more_than_equal'
     * @param   {mixed}  options.value
     * @return  {void}
     */
    updateFilterValue ({commit}, { fieldName, value }) {
        commit(types.UPDATE_FILTER_VALUE, { fieldName, value });
    },
};
