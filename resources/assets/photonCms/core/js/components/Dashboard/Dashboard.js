import { mapActions } from 'vuex';

import i18n from '_/i18n';

import Vue from 'vue';

import {
    Assets,
    LicenseExpiringNotification,
    MainMenu,
    QuickLaunch,
    TitleBar,
    UserMenu,
} from '@/components';

import DashboardRoute from '_/components/Dashboard/Dashboard.route';

const DashboardWidgets = Vue.component(
        'DashboardWidgets',
        require('_/components/Dashboard/Widgets/DashboardWidgets.vue')
    );

export default {
    /**
     * Set route handler
     *
     * @type {function}
     */
    route: DashboardRoute,

    components: {
        Assets,
        DashboardWidgets,
        LicenseExpiringNotification,
        'main-menu': MainMenu,
        UserMenu,
        TitleBar,
        QuickLaunch
    },

    /**
     * Set the methods
     *
     * @type {Object}
     */
    methods: {
        /**
         * Map actions from ui module namespace
         */
        ...mapActions('ui', [
            'animateWrapper',
            'setBodyClass',
            'setTitle'
        ]),
    },

    /**
     * Call a mounted hook
     *
     * @type {function}
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.setBodyClass('dashboard-page');

            this.setTitle(i18n.t('errors.dashboard'));

            this.animateWrapper();
        });
    }
};
