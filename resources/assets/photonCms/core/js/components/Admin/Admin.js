import {
    mapGetters,
    mapActions,
} from 'vuex';

import Vue from 'vue';

import { store } from '_/vuex/store';

import { storage } from '_/services/storage';

import { router } from '_/router/router';

import { eventBus } from '_/helpers/eventBus';

import {
    pError,
    pLog,
    pWarn,
} from '_/helpers/logger';

import * as adminSlots from '~/config/adminSlots';

import {
    Assets,
    Breadcrumb,
    EntryForm,
    HelpBlock,
    InfoPanel,
    Instructions,
    LicenseExpiringNotification,
    MainMenu,
    Sidebar,
    TitleBlock,
    UserMenu,
} from '@/components';

const Search = Vue.component(
        'Search',
        require('_/components/Admin/Search/Search.vue')
    );

import { getEntryFields } from '_/components/Admin/Admin.fields';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            /**
             * Stores the entry fields configuration object
             *
             * @type  {Array}
             */
            fields: [],

            /**
             * An object sent to child components; used to signal that the fields need to be reset, with the ability
             * to limit the reset to only certain fields
             *
             * @type  {Object}
             */
            formFieldsReset: {
                /**
                 * A list of field names to include in a reset action
                 *
                 * @type  {Array}
                 */
                includeFields: [],

                /**
                 * A parameter that is listened by the child components. Changed via moment().valueOf() method to always
                 * set the fresh value.
                 *
                 * @type  {integer}
                 */
                resetData: null,

                moduleTableName: this.$route.params.moduleTableName,
            },
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
            advancedSearch: 'advancedSearch/advancedSearch',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('admin', [
            'setAssetManagerSearchFilterObject',
        ]),

        ...mapActions('ui', [
            // TODO: Check if UI can work without animateWrapper action. If it can delete it project-wide.
            'animateWrapper',
            'setBodyClass',
        ]),

        ...mapActions('sidebar', [
            'setSidebarType',
        ]),

        /**
         * Sets the formFieldsReset data property in order to force refresh of the EntryForm component
         *
         * @param   {object}  options.includeFields The names of field components that need to be included in reset
         * @return  {void}
         */
        resetFormFields ({ includeFields = null } = {}) {
            this.formFieldsReset.resetData = moment().valueOf();

            this.formFieldsReset.includeFields = [];

            if(includeFields) {
                this.formFieldsReset.includeFields = includeFields;
            }

            eventBus.$emit('formFieldsReset', this.formFieldsReset);
        },
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        ...adminSlots,
        Assets,
        Breadcrumb,
        EntryForm,
        HelpBlock,
        InfoPanel,
        Instructions,
        LicenseExpiringNotification,
        MainMenu,
        Search,
        Sidebar,
        TitleBlock,
        UserMenu,
    },

    /**
     * Set the beforeRouteEnter hook
     *
     * @return  {void}
     */
    beforeRouteEnter: function (to, from, next) {
        const moduleTableName = to.params.moduleTableName;

        const shouldFetchSearch = to.params.moduleEntryId === 'search';

        if(shouldFetchSearch
            || moduleTableName === 'invitations'
            || moduleTableName === 'roles'
            || moduleTableName === 'permissions') {
            const licenseStatus = storage.get('licenseStatus', true);

            if (!(licenseStatus.domainType === 1 || licenseStatus.licenseType === 4)) {
                router.push('/error/resource-not-found');
            }
        }

        const moduleEntryId = !isNaN(to.params.moduleEntryId) ? to.params.moduleEntryId : null;

        if (!moduleTableName) {
            router.push('/error/resource-not-found');

            return;
        }

        pLog('Getting module information', moduleTableName);

        store.dispatch('admin/adminBootstrap', { moduleTableName, moduleEntryId, shouldFetchSearch })
            .then( () => {
                next();
            })
            .catch((error) => {
                pError('admin/adminBootstrap action failed with an error', error);

                router.push('/error/resource-not-found');
            });
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.fields = getEntryFields(this);

        this.$nextTick(function() {
            this.setBodyClass('admin');

            this.setSidebarType('AdminSidebar');

            this.animateWrapper();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        this.setSidebarType(null);

        // Clear the shared asset manager search filter
        this.setAssetManagerSearchFilterObject({ value: {}});
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        /**
         * Watching the route change
         *
         * @return  {void}
         */
        $route (newEntry, oldEntry) {
            const moduleTableName = newEntry.params.moduleTableName;

            const shouldFetchSearch = newEntry.params.moduleEntryId === 'search';

            const moduleEntryId = !isNaN(newEntry.params.moduleEntryId) ? newEntry.params.moduleEntryId : null;

            if (!moduleTableName) {
                router.push('/error/resource-not-found');

                return;
            }

            pWarn(
                'Watched property fired.',
                'Admin',
                '$route',
                newEntry,
                oldEntry,
                this
            );

            if(newEntry.params.moduleTableName !== oldEntry.params.moduleTableName) {
                // Clear the shared asset manager search filter
                this.setAssetManagerSearchFilterObject({ value: {}});
            }

            pLog('Refreshing module information', moduleTableName);

            store.dispatch('admin/adminBootstrap', { moduleTableName, moduleEntryId, shouldFetchSearch })
                .then(() => {
                    this.fields = getEntryFields(this);

                    this.resetFormFields();
                })
                .catch((error) => {
                    pError('admin/adminBootstrap action failed with an error', error);

                    router.push('/error/resource-not-found');
                });
        },

        'admin.entryUpdated' (newEntry, oldEntry) {
            pWarn(
                'Watched property fired.',
                'Admin',
                'admin.entryUpdated',
                newEntry,
                oldEntry,
                this
            );

            this.fields = getEntryFields(this);

            this.resetFormFields();
        },
    }
};
