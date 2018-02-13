import Vue from 'vue';
import Vuex from 'vuex';
import { config } from '_/config/config';

// Import vuex store modules
import adminFactory from '_/vuex/modules/admin';
import advancedSearchFactory from '_/vuex/modules/advancedSearch';
import assetsManagerFactory from '_/vuex/modules/assetsManager';
import error from '_/vuex/modules/error';
import gallery from '_/vuex/modules/gallery';
import generator from '_/vuex/modules/generator';
import menuEditor from '_/vuex/modules/menuEditor';
import menuItemsEditor from '_/vuex/modules/menuItemsEditor';
import notification from '_/vuex/modules/notification';
import photonField from '_/vuex/modules/photonField';
import photonModule from '_/vuex/modules/photonModule';
import sidebar from '_/vuex/modules/sidebar';
import subscription from '_/vuex/modules/subscription';
import ui from '_/vuex/modules/ui';
import user from '_/vuex/modules/user';
import widget from '_/vuex/modules/widget';

import { customModules } from '~/vuex/store';

Vue.use(Vuex);

/**
 * Export Vuex store with strict mode and middleware depending on config.ENV.debug setting for current environment
 *
 * @type {Vuex}
 */
export const store = new Vuex.Store({
    modules: {
        ...customModules,
        admin: adminFactory('admin'),
        adminModal: adminFactory('adminModal'),
        advancedSearch: advancedSearchFactory('advancedSearch'),
        assets: assetsManagerFactory('assets'),
        assetsAdvancedSearch: advancedSearchFactory('assetsAdvancedSearch'),
        error,
        gallery,
        generator,
        menuEditor,
        menuItemsEditor,
        notification,
        photonField,
        photonModule,
        subscription,
        sidebar,
        ui,
        user,
        widget,
    },
    strict: config.ENV.debug
});
