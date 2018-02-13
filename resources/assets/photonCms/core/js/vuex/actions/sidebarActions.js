import * as types from '_/vuex/mutation-types';

export default {
    /**
     * Updates sidebar type (used to display the corresponding sidebar component)
     *
     * @param   {function}  options.commit
     * @param   {string}  sidebarType
     * @return  {void}
     */
    setSidebarType({ commit }, sidebarType) {
        commit(types.UI_UPDATE_SIDEBAR_TYPE, sidebarType);
    }
};
