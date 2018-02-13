import {
    mapActions,
    mapGetters,
} from 'vuex';

import * as fieldComponents from '@/advancedSearchFieldTypeComponents';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        fields: {
            type: Array,
            required: true,
        },
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
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
            advancedSearch: 'advancedSearch/advancedSearch',
        }),
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data () {
        return {
            refreshFields: null,
        };
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('advancedSearch', [
            'clearAdvancedSearchFilters',
            'filterModuleEntries',
        ]),

        /**
         * Submits the search form
         *
         * @return  {void}
         */
        submitSearch () {
            const moduleTableName = this.$route.params.moduleTableName;

            const payload = this.advancedSearch.payload;

            this.filterModuleEntries({ moduleTableName, payload });
        },

        /**
         * Toggles the resetRefreshFields data property in order to force refresh of the RefreshFields component
         *
         * @return  {void}
         */
        toggleResetRefreshFields () {
            this.refreshFields = moment().valueOf();
        }
    },

    beforeDestroy () {
        this.clearAdvancedSearchFilters();
    },
};
