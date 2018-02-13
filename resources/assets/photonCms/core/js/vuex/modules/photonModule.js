import * as types from '_/vuex/mutation-types';

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    /**
     * A list of all modules that can be category modules
     *
     * @type  {Array}
     */
    categoryModules: [],

    /**
     * Module id to table name map
     *
     * @type  {Object}
     */
    moduleIdToTableNameMap: { },

    /**
     * Cached modules loaded from API
     *
     * @type  {Object}
     */
    moduleInformation: { },

    /**
     * A list of all modules
     *
     * @type  {object}
     */
    modules: null,

    /**
     * Module table name to id map
     *
     * @type  {Object}
     */
    moduleTableNameToIdMap: { }
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Performs a series of state changes after successfuly getting a list of all modules from the API
     *
     * @param  {object}  state
     * @param  {object}  modules
     * @return  {void}
     */
    [types.GET_ALL_MODULES_SUCCESS](state, { modules }) {
        state.modules = modules;

        state.categoryModules = modules.filter((module) => {
            state.moduleIdToTableNameMap[module.id] = module.table_name;

            state.moduleTableNameToIdMap[module.table_name] = module.id;

            if (module.type === 'sortable' || module.type === 'multilevel_sortable') {
                return true;
            }
        });
    },

    /**
     * Stores a module cache object, with key being module table name
     *
     * @param  {object}  state
     * @param  {object}  modul
     * @return  {void}
     */
    [types.GET_MODULE_INFORMATION_SUCCESS](state, {module}) {
        state.moduleInformation[module.table_name] = module;
    },

    /**
     * Deletes a module cache object, if not found via API
     *
     * @param  {object}  state
     * @param  {object}  modul
     * @return  {void}
     */
    [types.MODULE_NOT_FOUND](state, {table_name: moduleTableName}) {
        if (state.moduleInformation[moduleTableName]) {

            delete state.moduleInformation[moduleTableName];

        }
    }
};

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    photonModule: state => state,

    photonModules: state => {
        // TODO: should consider refactoring this so it's handled better in store like other stores,
        // not being initialized as null
        if (!state.modules || !state.modules.length) {
            return [];
        }

        return state.modules;
    },

    categoryModules: state => {
        return state.categoryModules;
    }
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/photonModuleActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
