import _ from 'lodash';

import { userHasRole } from '_/vuex/actions/userActions';

import {
    mapGetters,
    mapActions,
} from 'vuex';

import Vue from 'vue';

import { store } from '_/vuex/store';

import { router } from '_/router/router';

import { pError } from '_/helpers/logger';

import {
    destroyJsTree,
    setupJsTree
} from '_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.jsTree';

import {
    Assets,
    Breadcrumb,
    HelpBlock,
    LicenseExpiringNotification,
    MainMenu,
    Sidebar,
    TitleBlock,
    UserMenu,
} from '@/components';

const ModuleConfigurator = Vue.component(
        'ModuleConfigurator',
        require('_/components/Generator/ModuleConfigurator/ModuleConfigurator.vue')
    );

const GeneratorInfoPanel = Vue.component(
        'GeneratorInfoPanel',
        require('_/components/Generator/GeneratorInfoPanel/GeneratorInfoPanel.vue')
    );

const GeneratorHelpBlock = Vue.component(
        'GeneratorHelpBlock',
        require('_/components/Generator/GeneratorHelpBlock/GeneratorHelpBlock.vue')
    );

export default {
    /**
     * Set the beforeRouteEnter hook
     *
     * @return  {void}
     */
    beforeRouteEnter: function (to, from, next) {
        const moduleTableName = !_.isUndefined(to.params.moduleTableName) ? to.params.moduleTableName : false;

        store.dispatch('photonModule/getPhotonModules', { refreshList: true })
            .then(() => {
                if (moduleTableName) {
                    if (!store.state.photonModule.moduleTableNameToIdMap[moduleTableName]) {
                        pError('Photon module doesn\'t exist.', moduleTableName);

                        router.push('/error/resource-not-found');

                        return;
                    }
                }

                store.dispatch('generator/selectGeneratorModule', { moduleTableName })
                    .then(() => {
                        store.dispatch('generator/setGeneratorUi');

                        next();
                    });
            })
            .catch((error) => {
                pError('photonModule/getPhotonModules action failed with an error', error);

                router.push('/error/resource-not-found');
            });
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            generator: 'generator/generator',
            ui: 'ui/ui',
        })
    },

    /**
     * Set the components
     *
     * @type  {Object}
     */
    components: {
        Assets,
        Breadcrumb,
        GeneratorHelpBlock,
        GeneratorInfoPanel,
        HelpBlock,
        LicenseExpiringNotification,
        MainMenu,
        ModuleConfigurator,
        Sidebar,
        TitleBlock,
        UserMenu,
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('generator', [
            'generatorCleanup',
            'selectGeneratorModule',
            'setGeneratorUi',
        ]),

        ...mapActions('photonModule', [
            'getPhotonModules',
            'getPhotonModuleInformation',
        ]),

        ...mapActions('sidebar', [
            'setSidebarType',
        ]),

        ...mapActions('ui', [
            'animateWrapper',
            'setBodyClass',
        ]),

        /**
         * getPhotonModules method wrapper
         *
         * @return  {promise}
         */
        getPhotonModulesHelper(moduleTableName) {
            return this.getPhotonModules({ refreshList: true })
                .then(() => {
                    if (moduleTableName) {
                        if (!store.state.photonModule.moduleTableNameToIdMap[moduleTableName]) {
                            pError('Photon module doesn\'t exist.', moduleTableName);

                            router.push('/error/resource-not-found');

                            return;
                        }
                    }

                    return this.selectGeneratorModule({ moduleTableName })
                        .then(() => {
                            return this.setGeneratorUi();
                        });
                })
                .catch((error) => {
                    pError('photonModule/getPhotonModules action failed with an error', error);

                    router.push('/error/resource-not-found');
                });
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            if(!userHasRole('super_administrator')) {
                router.push('/error/resource-not-found');
            }

            this.setBodyClass('generator');

            this.setSidebarType('GeneratorSidebar');

            this.animateWrapper();

            this.setGeneratorUi();
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        $route (newEntry) {
            const moduleTableName = newEntry.params.moduleTableName;

            this.getPhotonModulesHelper(moduleTableName);
        },

        'generator.refreshForm' () {
            const moduleTableName = this.generator.selectedModule.table_name;

            this.getPhotonModulesHelper(moduleTableName)
                .then(() => {
                    destroyJsTree();

                    setupJsTree(this);
                });
        }
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        this.generatorCleanup();

        this.setSidebarType(null);
    }
};
