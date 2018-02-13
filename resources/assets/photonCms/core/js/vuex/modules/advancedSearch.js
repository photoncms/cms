import _ from 'lodash';

import * as types from '_/vuex/mutation-types';

const photonConfig = require('~/config/config.json');

import Vue from 'vue';

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/advancedSearchActions';

export default function advancedSearchFactory (advancedSearchName) {
    /**
     * Define the module state
     *
     * @type  {object}
     */
    const state = {
        /**
         * Stores the filtered entries returned by the API filter route
         *
         * @type  {Object}
         */
        entries: {},

        /**
         * Stores the entries pagination object
         *
         * @type  {Object}
         */
        entriesPagination: {},

        /**
         * Stores this module name
         */
        advancedSearchName,

        /**
         * Stores the payload object used to filter the entries in an API call
         *
         * @type  {Object}
         */
        payload: {
            /**
             * Search filter payload
             *
             * @type  {Object}
             */
            filter: {},

            /**
             * Excluded related entries to load results faster
             *
             * @type  {boolean}
             */
            include_relations: false,

            /**
             * Search pagination payload
             *
             * @type  {Object}
             */
            pagination: {
                current_page: 1,
                items_per_page: photonConfig.searchItemsPerPage,
            },

            /**
             * Set the sorting option
             *
             * @type  {Object}
             */
            sorting: {
                updated_at: 'desc',
            },
        },

        /**
         * Used to switch the mass editor mode on/off
         *
         * @type  {boolean}
         */
        showMassEditor: false,
    };

    /**
     * Sets a nested state object value
     *
     * @param   {object}  state
     * @param   {object}  objectReference
     * @param   {string}  path
     * @param   {object}  value
     * @return  {void}
     */
    const _setNestedObjectValue = (state, objectReference, path, value) => {
        const objectPath = path.split('.');

        const valueKey = objectPath.pop();

        let currentObjectReference = objectReference;

        objectPath.forEach(function(elem) {
            if (Object.prototype.toString.call(currentObjectReference[elem]) !== '[object Object]') {
                Vue.set(currentObjectReference, elem, {});
            }

            currentObjectReference = currentObjectReference[elem];
        }, 'payload');

        Vue.set(currentObjectReference, valueKey, value);
    };

    /**
     * Unsets a nested state object value
     *
     * @param   {object}  state
     * @param   {object}  objectReference
     * @param   {string}  path
     * @return  {void}
     */
    const _unsetNestedObjectValue = (state, objectReference, path) => {
        if (_.has(objectReference, path)) {
            _.unset(objectReference, path);
        }

        const objectPath = path.split('.');

        objectPath.pop();

        _removeEmptyObjectsFromNestedObject(state, objectReference, objectPath);
    };

    /**
     * Removes empty state objects from nested state object
     *
     * @param   {object}  state
     * @param   {object}  objectReference
     * @param   {array}  objectPath
     * @return  {void}
     */
    const _removeEmptyObjectsFromNestedObject = (state, objectReference, objectPath) => {
        if (objectPath.length == 1 && objectPath[0] == 'filter') { // don't remove the root element
            return;
        }

        const valueKey = objectPath.pop();

        let currentObjectReference = objectReference;

        objectPath.forEach(function(elem) {
            currentObjectReference = currentObjectReference[elem];
        });

        if (_.isObject(currentObjectReference)
            && _.isObject(currentObjectReference[valueKey])
            && _.isEmpty(currentObjectReference[valueKey])) {
            Vue.delete(currentObjectReference, valueKey);

            _removeEmptyObjectsFromNestedObject(state, objectReference, objectPath);
        }
    };

    /**
     * Define the module mutations
     *
     * @type  {object}
     */
    const mutations = {
        /**
         * Clears the advancedSearch state property
         *
         * @param  {object}  state
         * @return  {void}
         */
        [types.CLEAR_ADVANCED_SEARCH_FILTER] (state) {
            Vue.set(state.payload, 'filter', {});
        },

        /**
         * Setst the payload.entriesPagination state property
         *
         * @param  {object}  state
         * @param  {object}  options.pagination
         */
        [types.LOAD_DYNAMIC_MODULE_ENTRIES_PAGINATION](state, { pagination }) {
            state.entriesPagination = pagination;
        },

        /**
         * Sets the advancedSearch state property
         *
         * @param  {object}  state
         * @param  {object}  options.value
         * @return  {void}
         */
        [types.SET_ADVANCED_SEARCH_FILTER] (state, { value }) {
            Vue.set(state.payload, 'filter', value);
        },

        /**
         * Sets the showMassEditor scope property
         *
         * @param  {object}  state
         * @param  {boolean}  options.value
         * @return  {void}
         */
        [types.SET_MASS_EDITOR_VISIBILITY] (state, { value }) {
            state.showMassEditor = value;
        },

        /**
         * Set the entries state property
         *
         * @param  {object}  state
         * @param  {object}  options.entries
         * @return  {void}
         */
        [types.UPDATE_FILTERED_ENTRIES] (state, { entries }) {
            state.entries = entries;
        },

        /**
         * Sets the current_page state property
         *
         * @param  {object}  state
         * @param  {integer}  options.value
         * @return  {void}
         */
        [types.UPDATE_CURRENT_PAGE_NUMBER] (state, { value }) {
            state.payload.pagination.current_page = value;
        },

        /**
         * Sets the advanced search filter state property
         *
         * @param  {object}  state
         * @param  {integer}  options.fieldName
         * @param  {object}  options.value
         * @return  {void}
         */
        [types.UPDATE_FILTER_VALUE] (state, { fieldName, value }) {
            if (value !== '') {
                _setNestedObjectValue(state, state.payload, fieldName, value);

                return;
            }

            _unsetNestedObjectValue(state, state.payload, fieldName);
        },
    };

    /**
     * Define the module getters
     *
     * @type {object}
     */
    const getters = {};

    getters[advancedSearchName] = state => state;


    return {
        actions,
        getters,
        mutations,
        namespaced: true,
        state
    };
}
