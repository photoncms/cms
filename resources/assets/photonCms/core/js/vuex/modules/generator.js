import _ from 'lodash';

import * as types from '_/vuex/mutation-types';

import * as fieldTypesCore from '_/services/fieldTypes';

import * as fieldTypesDependencies from '~/services/fieldTypes';

import { pError } from '_/helpers/logger';

import Vue from 'vue';

const fieldTypes = {
    ...fieldTypesCore,
    ...fieldTypesDependencies,
};

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    /**
     * Object used to disply errors returned by the API
     *
     * @type  {Object}
     */
    error: {
        fields: null,
        message: null,
    },

    /**
     * Not sure if this will be used at all
     *
     * @type  {boolean}
     */
    multipleSelection: false,

    /**
     * New fields get ids in negative index
     *
     * @type  {integer}
     */
    newFieldId: -1,

    /**
     * Denotes if new module is being created or old one is being edited
     *
     * @type  {boolean}
     */
    newModule: true,

    /**
     * A list of all modules, displayed in the generator sidebar
     *
     * @type  {Array}
     */
    nodes: [],

    /**
     * Report data
     *
     * @type  {object}
     */
    report: null,

    /**
     * Report type (create, update, delete)
     *
     * @type  {string}
     */
    reportType: null,

    /**
     * The property watched by Generator modules, used to trigger the form refresh
     *
     * @type  {integer}
     */
    refreshForm: null,


    /**
     * Module selected for editing
     *
     * @type  {object}
     */
    selectedModule: null,
};

/**
 * Creates the single generator node object (representing  single Photon module)
 *
 * @param   {object}  photonModule
 * @return  {object}
 */
const createGeneratorNode = photonModule => {
    const node = {
        icon: photonModule.icon,
        id: photonModule.id,
        parent: '#',
        tableName: photonModule.table_name,
        text: photonModule.name,
    };

    return node;
};

/**
 * Updates a single field property
 *
 * @param   {object}  state
 * @param   {integer}  options.id
 * @param   {any}  options.newValue
 * @return  {void}
 */
const updateFieldProperty = (state, { id, newValue }) => {
    // Parse input id to get field order
    const parsedId = id.split('|');

    const fieldId = parseInt(parsedId[0], 10);

    const fieldProperty = parsedId[1];

    let field = state.selectedModule.fields.find(field => field.id === fieldId);

    if (!field) {
        pError('No field found with a specified id:', field.id);

        return;
    }

    field[fieldProperty] = newValue;
};

/**
 * Processes report
 *
 * @param   {object}  apiReport
 * @return  {object}
 */
const processReport = apiReport => {
    const processedReport = {};

    for (let apiReportItem in apiReport) {
        if (!apiReport.hasOwnProperty(apiReportItem)) {
            continue;
        }

        processReportItem(apiReportItem, apiReport, processedReport);
    }

    return processedReport;
};

/**
 * Processes a single report item
 *
 * @param   {object}  apiReportItem
 * @param   {object}  apiReport
 * @param   {object}  output
 * @return  {object}
 */
const processReportItem = (apiReportItem, apiReport, output) => {
    if (apiReportItem === 'modules') {
        output.module = apiReport[apiReportItem];

        return;
    }

    if (apiReportItem === 'fields') {
        output.field = [];

        const fieldsReport = apiReport[apiReportItem];

        for (var i = 0; i < fieldsReport.length; i++) {
            if (fieldsReport[i].data.length === 0 && fieldsReport[i].change_type != 'delete') {
                continue;
            }

            if (fieldsReport[i].data.module_id) {
                delete fieldsReport[i].data.module_id;
            }

            if (!fieldsReport[i].data.type) {
                output.field.push(fieldsReport[i]);
                continue;
            }

            if (!fieldsReport[i].data.type.old) {
                fieldsReport[i].data.type.old = null;
            } else {
                fieldsReport[i].data.type.old = fieldTypes.mapFromId[fieldsReport[i].data.type.old].name;
            }

            if (!fieldsReport[i].data.type.old) {
                fieldsReport[i].data.type.new = fieldTypes.mapFromId[fieldsReport[i].data.type.new].name;
            } else {
                fieldsReport[i].data.type.old = null;
            }

            output.field.push(fieldsReport[i]);
        }

        return;
    }
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    [types.CLEAR_GENERATOR_ERRORS](state) {
        state.error.message = null;

        state.error.fields = null;
    },

    [types.CLEAR_MODULE_CREATION_REPORT](state) {
        state.report = false;

        state.reportAction = false;
    },

    [types.CREATE_GENERATOR_SELECTED_MODULE_FIELD]() {
        state.selectedModule.fields = [
            ...state.selectedModule.fields, {
                column_name: null,
                editable: true,
                id: state.newFieldId,
                name: null,
                nullable: true,
                newField: true,
                order: state.selectedModule.fields.length,
                pivot_table: null,
                related_module: null,
                relation_name: null,
                tooltip_text: null,
                type: fieldTypes.inputText.id,
                validation_rules: null,
            }
        ];
        state.newFieldId = state.newFieldId - 1; // update id for any new fields
    },

    [types.DELETE_GENERATOR_SELECTED_MODULE_FIELD](state, fieldToDelete) {
        const fieldToDeleteIndex = state.selectedModule.fields.findIndex(field => field.id === fieldToDelete.id);

        state.selectedModule.fields.splice(fieldToDeleteIndex, 1);
    },

    [types.GENERATOR_ERROR_DISPATCH](state, { errorMessage, apiResponse }) {
        state.error.message = errorMessage;

        state.error.fields = apiResponse.error_fields ? apiResponse.error_fields : null;
    },

    [types.GENERATOR_CLEANUP](state) {
        state.error = {
            fields: null,
            message: null,
        };

        state.multipleSelection = false;

        state.newModule = true;

        state.report = null;

        state.reportType = null;

        state.selectedModule = null;
    },

    [types.MODULE_CREATION_REPORT_SUCCESS](state, apiReport) {
        state.reportType = 'create';

        state.report = processReport(apiReport);
    },

    [types.MODULE_CREATION_SUCCESS]() {},

    [types.MODULE_DELETE_REPORT_SUCCESS](state) {
        state.reportType = 'delete';

        state.report = [];
    },

    [types.MODULE_DELETION_SUCCESS]() {},

    [types.MODULE_UPDATE_REPORT_SUCCESS](state, apiReport) {
        state.reportType = 'update';

        state.report = processReport(apiReport);
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE](state, selectedModule) {
        // const selectedNode = state.nodes.find(node => node.id === selectedModule.id)
        if (!selectedModule) {
            const templateModule = {
                anchor_text: null,
                anchor_html: null,
                category: 0,
                fields: [],
                group_id: null,
                icon: 'fa fa-bars',
                name: null,
                slug: null,
                table_name: null,
                type: 'non_sortable',
            };

            state.newModule = true;

            state.selectedModule = templateModule;

            return;
        }

        if (!selectedModule.category) {
            selectedModule.category = 0;
        }

        state.selectedModule = selectedModule;

        state.newModule = false;
    },

    [types.POPULATE_NODES](state, { modules }) {
        // Processes generator tree nodes from Photon modules array
        state.nodes = modules.map((photonModule) => {
            return createGeneratorNode(photonModule);
        });
    },

    [types.SET_GENERATOR_REFRESH_FORM](state, { value }) {
        state.refreshForm = value;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_ANCHOR_HTML](state, {newValue}) {
        state.selectedModule.anchor_html = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_ANCHOR_TEXT](state, {newValue}) {
        state.selectedModule.anchor_text = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_CATEGORY](state, {newValue}) {
        state.selectedModule.category = newValue;
    },

    [types.UPDATE_GENERATOR_FIELD_PROPERTY]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_CAN_CREATE_SEARCH_CHOICE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_COLUMN_NAME]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_DEFAULT]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_DISABLED]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_EDITABLE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_FLATTEN_TO_OPTGROUPS]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_FOREIGN_KEY]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_HIDDEN]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_ID]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_INDEXED]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_ACTIVE_ENTRY_FILTER]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_DEFAULT_SEARCH_CHOICE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_SYSTEM]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_LAZY_LOADING]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_LOCAL_KEY]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_NAME]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_NULLABLE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_ORDER](state, { newOrder }) {
        let fields = state.selectedModule.fields;

        const newOrderLength = newOrder.length;

        for (var key = 0; key < newOrderLength; key++) {

            let value = newOrder[key];

            let field = _.find(fields, { 'id': parseInt(value) });

            field['order'] = key;
        }

        Vue.set(state.selectedModule, 'fields', _.orderBy(fields, ['order'], ['asc']));
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_PIVOT_TABLE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_RELATED_MODULE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_RELATION_NAME]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_TOOLTIP_TEXT]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_TYPE]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_FIELD_VALIDATION_RULES]: updateFieldProperty,

    [types.UPDATE_GENERATOR_SELECTED_MODULE_ICON](state, {newValue}) {
        state.selectedModule.icon = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_NAME](state, {newValue}) {
        state.selectedModule.name = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_SLUG](state, {newValue}) {
        state.selectedModule.slug = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_TABLE_NAME](state, {newValue}) {
        state.selectedModule.table_name = newValue;
    },

    [types.UPDATE_GENERATOR_SELECTED_MODULE_TYPE](state, {newValue}) {
        state.selectedModule.type = newValue;
    },
};

/**
 * Define the module getters
 *
 * @type {object}
 */
const getters = {
    generator: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/generatorActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state,
};
