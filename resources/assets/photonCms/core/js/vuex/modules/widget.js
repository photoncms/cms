import * as types from '_/vuex/mutation-types';

import _ from 'lodash';

import Vue from 'vue';

import { pError } from '_/helpers/logger';

/**
 * Define the module state
 *
 * @type {object}
 */
const state = {
    widgets: [],
    widgetsModuleFields: [],
};

/**
 * Define the module mutations
 *
 * @type {object}
 */
const mutations = {
    /**
     * Populates the widgets array
     *
     * @param   {object}  options.state
     * @param   {array}  options.values
     * @return  {void}
     */
    [types.GET_WIDGETS_SUCCESS](state, { values }) {
        state.widgets = [
            ...state.widgets,
            ...values,
        ];
    },

    /**
     * Creates the new widget object
     *
     * @param   {object}  options.state
     * @param   {object}  options.widget
     * @return  {void}
     */
    [types.CREATE_NEW_WIDGET](state, { widget }) {
        state.widgets.push(widget);
    },

    /**
     * Deletes a widget object from widgets state property
     *
     * @param   {object}  options.state
     * @param   {integer}  options.widgetId
     * @return  {void}
     */
    [types.DELETE_WIDGET](state, { widgetId }) {
        const index = _.findIndex(state.widgets, { 'id': widgetId });

        Vue.delete(state.widgets, index);
    },

    /**
     * Resets a widget model
     *
     * @param   {object}  options.state
     * @return  {void}
     */
    [types.RESET_WIDGET_MODEL](state) {
        state.widgets = [];

        state.widgetsModuleFields = [];
    },

    /**
     * Updates a widget object
     *
     * @param   {object}  options.state
     * @param   {object}  options.widget
     * @return  {void}
     */
    [types.UPDATE_WIDGET](state, { widget }) {
        const index = _.findIndex(state.widgets, { 'id': widget.id });

        Vue.set(state.widgets, index, widget);
    },

    /**
     * Updates the single widget property
     *
     * @param   {object}  options.state
     * @param   {string}  options.id
     * @param   {string}  options.newValue
     * @return  {void}
     */
    [types.UPDATE_WIDGET_PROPERTY](state, { id, newValue }) {
        const parsedId = id.split('|');

        const fieldId = parseInt(parsedId[0], 10);

        const fieldProperty = parsedId[1];

        let widget = state.widgets.find(widget => widget.id === fieldId);

        if (!widget) {
            pError('No widget found with a specified id:', widget.id);

            return;
        }

        widget[fieldProperty] = newValue;
    },

    /**
     * Updates the widgetsModuleFields property
     *
     * @param   {object}  options.state
     * @param   {array}  options.fields
     * @return  {void}
     */
    [types.UPDATE_WIDGET_MODULE_FIELDS](state, { fields }) {
        state.widgetsModuleFields = fields;
    },
};

/**
 * Define the module getters
 *
 * @type {object}
 */
const getters = {
    widget: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type {object}
 */
import actions from '_/vuex/actions/widgetActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state,
};
