import { getEntryFields } from '_/components/Admin/Admin.fields';

import {
    mapActions,
    mapGetters,
} from 'vuex';

import { store } from '_/vuex/store';

import * as fieldComponents from '@/advancedSearchFieldTypeComponents';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        assetsManager: {
            type: Object,
            required: true,
        },
    },

    /**
     * Setup the beforeCreate hook
     *
     * @return  {void}
     */
    beforeCreate () {
        /**
         * Then AdvancedSearch component is loaded recursively, and the proposed method that suggests that it's OK to have
         * recursive components as long as you have a unique name for each failed.
         * Evan suggested this undocumented solution in https://github.com/vuejs/vue/issues/4117 to fix the issue.
         */
        this.$options.components.AdvancedSearch = require('_/components/UserInterface/Sidebar/AdminSidebar/AdvancedSearch/AdvancedSearch.vue');
    },

    beforeDestroy () {
        this.clearAdvancedSearchFilters();
    },

    /**
     * Define the components
     *
     * @type  {Object}
     */
    components: {
        ...fieldComponents,
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            /**
             * Stores the entry fields configuration object
             *
             * @type  {Array}
             */
            fields: [],
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            assetsAdvancedSearch: 'assetsAdvancedSearch/assetsAdvancedSearch',
            photonModule: 'photonModule/photonModule',
        }),

        /**
         * Map the assetManager model as admin
         *
         * @return  {object}
         */
        admin () {
            return this.assetsManager;
        },
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

        ...mapActions('assetsAdvancedSearch', [
            'clearAdvancedSearchFilters',
        ]),

        /**
         * Submits the search form
         *
         * @return  {void}
         */
        submitSearch () {
            store.dispatch(`${this.assetsManager.moduleName}/setFilterObject`, { value: this.assetsAdvancedSearch.payload.filter })
                .then(() => {
                    // Remember the search filter in admin module as well
                    this.setAssetManagerSearchFilterObject({ value: this.assetsAdvancedSearch.payload.filter })
                        .then(() => {
                            this.$emit('submitSearch');
                        });
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
            store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName: 'assets' })
                .then(() => {
                    const selectedModule = this.photonModule.moduleInformation.assets;

                    store.dispatch(`${this.assetsManager.moduleName}/updateSelectedModule`, { module: selectedModule })
                        .then(() => {
                            this.fields = getEntryFields(this);
                        });
                });
        });
    },
};
