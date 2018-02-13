import * as types from '_/vuex/mutation-types';

/**
 * Imports config.json as a javascript object
 *
 * @type  {object}
 */
const photonConfig = require('~/config/config.json');

/**
 * Define the module state
 *
 * @type  {object}
 */
const state = {
    bodyWidth: 0,
    breadcrumbItems: [],
    // If set to false 'No entries found' message will appear instead of the jstree
    hasNodes: true,
    icon: null,
    /**
     * Stores menus data
     *
     * @type  {Object}
     */
    menus: {
        /**
         * Stores the main menu data
         *
         * @type  {Array}
         */
        mainMenu: [],

        /**
         * Stores the quick launch menu data
         *
         * @type  {Array}
         */
        quickLaunchMenu: [],

        /**
         * Stores the user menu data
         *
         * @type  {Array}
         */
        userMenu: [],
    },
    pageType: null,
    photonConfig: photonConfig,
    screenMd: 992,
    screenXs: 480,
    searchMode: false,
    sidebarExtended: true,
    subtext: null,
    title: null,
};

/**
 * Define the module mutations
 *
 * @type  {object}
 */
const mutations = {
    /**
     * Sets the page title
     *
     * @param  {object}  state
     * @param  {string}  title
     * @return  {void}
     */
    [types.UI_SET_TITLE](state, title) {
        state.title = title;
    },

    /**
     * Stores the main menu data to state
     *
     * @param  {object}  state
     * @param  {string}  title
     * @return  {void}
     */
    [types.UI_LOAD_MAIN_MENU_SUCCESS](state, menuData) {
        const data = repackTreeData(menuData);

        state.menus.mainMenu = data;
    },

    /**
     * Stores the quick launch menu data to state
     *
     * @param  {object}  state
     * @param  {string}  title
     * @return  {void}
     */
    [types.UI_LOAD_QUICK_LAUNCH_MENU_SUCCESS](state, menuData) {
        const data = menuData.map(function (node) {
            node.resource_data = node.menu_link_type_name !== 'static_link'
                ? JSON.parse(node.resource_data)
                : node.resource_data;

            node.entry_data = node.entry_data !== null
                ? JSON.parse(node.entry_data)
                : null;

            return node;
        });

        state.menus.quickLaunchMenu = data;
    },

    /**
     * Stores the user menu data to state
     *
     * @param  {object}  state
     * @param  {string}  title
     * @return  {void}
     */
    [types.UI_LOAD_USER_MENU_SUCCESS](state, menuData) {
        state.menus.userMenu = menuData;
    },

    /**
     * Sets the subtext
     *
     * @param  {object}  state
     * @param  {string}  subtext
     * @return  {void}
     */
    [types.UI_SET_TITLE_SUBTEXT](state, subtext) {
        state.subtext = subtext;
    },

    /**
     * Sets the icon
     *
     * @param  {object}  state
     * @param  {string}  icon
     * @return  {void}
     */
    [types.UI_SET_TITLE_ICON](state, icon) {
        state.icon = icon;
    },

    /**
     * Sets the page body width
     *
     * @param  {object}  state
     * @param  {int}  newWidth         Unit-less pixel value
     * @return  {void}
     */
    [types.UI_BODY_RESIZE](state, newWidth) {
        state.bodyWidth = newWidth;
    },

    /**
     * Sets the breadcrumb object
     *
     * @param  {object}  state
     * @param  {array}  newBreadcrumbItems    Accepts an array consisting of object(s) with text and link values
     * @return  {void}
     */
    [types.UI_UPDATE_BREADCRUMB_ITEMS](state, newBreadcrumbItems) {
        if (newBreadcrumbItems.length) {
            newBreadcrumbItems[newBreadcrumbItems.length - 1].active = true; // If breadcrumb items are passed (not empty array) mark last one as active
        }
        state.breadcrumbItems = newBreadcrumbItems;
    },

    /**
     * Sets the hasNodes state property
     */
    [types.UI_UPDATE_HAS_NODES](state, { hasNodes }) {
        state.hasNodes = hasNodes;
    },

    /**
     * Sets the sidebar state
     *
     * @param  {object}  state
     * @param  {bool}  sidebarExtended
     * @return  {void}
     */
    [types.UI_SIDEBAR_EXTENDED_STATE](state, sidebarExtended) {
        state.sidebarExtended = sidebarExtended;
    },
};

const repackTreeData = function(nodes) {
    let map = {};

    let node;

    let roots = [];

    for (var i = 0; i < nodes.length; i += 1) {
        node = nodes[i];

        node.resource_data = node.menu_link_type_name !== 'static_link'
            ? JSON.parse(node.resource_data)
            : node.resource_data;

        node.children = [];

        map[node.id] = i;

        if (node.parent_id) {
            nodes[map[node.parent_id]].children.push(node);
        } else {
            roots.push(node);
        }
    }

    return roots;
};

/**
 * Define the module getters
 *
 * @type  {object}
 */
const getters = {
    ui: state => state
};

/**
 * Import the module actions as an object containing action methods
 *
 * @type  {object}
 */
import actions from '_/vuex/actions/uiActions';

export default {
    actions,
    getters,
    mutations,
    namespaced: true,
    state
};
