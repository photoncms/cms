import * as types from '_/vuex/mutation-types';

import { api } from '_/services/api';

import { errorCommit } from '_/vuex/actions/commonActions';

import { router } from '_/router/router';

import i18n from '_/i18n';

export default {
    /**
     * Animate function shows .sidebar, .wrapper after a small timeout to prevent flicker
     *
     * @return  {void}
     */
    animateWrapper() {
        setTimeout(() => {
            $('.sidebar, .wrapper').css('opacity', '1');
        }, 100);
    },

    /**
     * Retrieves the main menu.
     *
     * @param   {function}  options.dispatch
     * @return  {promise}
     */
    getMainMenu({ dispatch }) {
        dispatch('getMenu', { menuId: 1, mutationName: 'UI_LOAD_MAIN_MENU_SUCCESS' });
    },

    /**
     * Retrieves the quick launch menu.
     *
     * @param   {function}  options.dispatch
     * @return  {promise}
     */
    getQuickLaunchMenu({ dispatch }) {
        dispatch('getMenu', { menuId: 2, mutationName: 'UI_LOAD_QUICK_LAUNCH_MENU_SUCCESS' });
    },

    /**
     * Retrieves the menu by menuId.
     *
     * @param  {function}  options.commit
     * @param  {integer}  options.menuId
     * @param  {string}  options.mutationName
     * @return  {promise}
     */
    getMenu({ commit }, { menuId, mutationName }) {
        return api.get(`menus/${menuId}/items`)
            .then((response) => {
                commit(types[mutationName], response.body.body.menu_items);
            })
            .catch((response) => {
                errorCommit({ commit }, response);
            });
    },

    /**
     * Retrieves the user menu.
     *
     * @param   {function}  options.dispatch
     * @return  {promise}
     */
    getUserMenu({ dispatch }) {
        dispatch('getMenu', { menuId: 2, mutationName: 'UI_LOAD_USER_MENU_SUCCESS' });
    },

    /**
     * Sets the class of a body HTML element
     *
     * @param  {function}  options.commit
     * @param  {string}  newBodyClass
     * @return  {void}
     */
    setBodyClass({commit}, newBodyClass) {
        $('body').attr('class', newBodyClass);
    },

    /**
     * Sets the document title and commits the UI_SET_TITLE mutation
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {string}  title
     * @return  {void}
     */
    setTitle({ commit, state }, title) {
        document.title = title + ' - ' + state.photonConfig.companyTitle;

        commit(types.UI_SET_TITLE, title);
    },

    /**
     * Commits the UI_SET_TITLE_SUBTEXT mutation
     *
     * @param  {function}  options.commit
     * @param  {string}  subtext
     * @return  {void}
     */
    setTitleSubtext({commit}, subtext) {
        commit(types.UI_SET_TITLE_SUBTEXT, subtext);
    },

    /**
     * Commits the UI_SET_TITLE_ICON mutation
     *
     * @param  {function}  options.commit
     * @param  {string}  icon
     * @return  {void}
     */
    setTitleIcon({commit}, icon) {
        commit(types.UI_SET_TITLE_ICON, icon);
    },

    /**
     * Commits the UI_BODY_RESIZE mutation to set the page body width
     *
     * @param  {function}  options.commit
     * @param  {int}  newWidth         Unit-less pixel value
     * @return  {void}
     */
    bodyResize({commit}, newWidth) {
        commit(types.UI_BODY_RESIZE, newWidth);
    },

    /**
     * Commits the UI_UPDATE_BREADCRUMB_ITEMS mutation
     *
     * @param  {function}  options.commit
     * @param  {array}  newBreadcrumbItems Accepts an array consisting of object(s) with text and link values
     * @return  {void}
     */
    setBreadcrumbItems({commit}, newBreadcrumbItems) {
        commit(types.UI_UPDATE_BREADCRUMB_ITEMS, newBreadcrumbItems);
    },

    /**
     * Triggers a switch to create mode by changing the route.
     *
     * @return  {void}
     */
    triggerCreateMode () {
        const currentRoute = router.app.$route.path;

        // Strip trailing slashes
        let newRoute = currentRoute.replace(/\/+$/, '');

        // Strip trailing numbers
        newRoute = newRoute.replace(/\d+$/, '');

        // Strip trailing slashes again
        newRoute = newRoute.replace(/\/+$/, '');

        // Reroute to new route
        router.push(newRoute);
    },

    /**
     * Sets the visibility of 'No entries found' text if jstree doesn't have any items to show
     *
     * @param   {function}  options.commit
     * @param   {boolean}  options.hasNodes
     * @return  {void}
     */
    updateHasNodes({ commit }, { hasNodes }) {
        commit(types.UI_UPDATE_HAS_NODES, { hasNodes });
    },
};
