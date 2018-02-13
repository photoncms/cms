/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
};

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    gallery: state => state,
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/galleryActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
