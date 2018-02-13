import * as types from '_/vuex/mutation-types';

/**
 * Load the uppercamelcase helper function
 *
 * @type  {function}
 */
const upperCamelCase = require('uppercamelcase');

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    fieldTypes: {},  // A list of all custom fields
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Creates a list of all field types and stores them indexed by ID
     *
     * @param  {object}  state
     * @param  {array}  field_types
     * @return  {void}
     */
    [types.GET_ALL_FIELD_TYPES_SUCCESS](state, {field_types}) {
        let types = {};

        field_types.forEach(function(fieldType) {
            fieldType.component = upperCamelCase(fieldType.type);
            types[fieldType.id] = fieldType;
        });

        state.fieldTypes = types;
    }
};


/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    photonField: state => state,
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/photonFieldActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
