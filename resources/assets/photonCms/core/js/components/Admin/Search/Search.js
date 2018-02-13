import {
    mapGetters,
    mapActions,
} from 'vuex';

import Vue from 'vue';

const photonConfig = require('~/config/config.json');

const MassEditor = Vue.component(
        'MassEditor',
        require('_/components/Admin/MassEditor/MassEditor.vue')
    );

export default {
    data: function() {
        return {
            fields: [],
            serverError: null,
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        MassEditor,
    },

    computed: {
        ...mapGetters({
            admin: 'admin/admin',
            advancedSearch: 'advancedSearch/advancedSearch',
            ui: 'ui/ui',
        }),

        /**
         * Returns the photonConfig object
         *
         * @return  {object}
         */
        photonConfig () {
            return photonConfig;
        },

        trimmedPagination () {
            let pages = [];

            const currentPage = this.pagination.current_page;

            const totalPages = this.pagination.last_page;

            for(let page = Math.max(1, currentPage - 3); page <= Math.min(currentPage + 3, totalPages); page++) {
                pages.push(page);
            }

            return pages;
        },

        /**
         * Date format getter
         *
         * @return  {string}
         */
        dateFormat () {
            return this.ui.photonConfig.dateFormat;
        },

        selectedModule () {
            return this.admin.selectedModule.table_name;
        },

        results () {
            return this.advancedSearch.entries;
        },

        pagination () {
            return this.advancedSearch.entriesPagination;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('advancedSearch', [
            'hideMassEditor',
            'navigateToEntry',
            'navigateToPage',
            'showMassEditor',
        ]),

        /**
         * Created at getter
         *
         * @param   {integer}  entry
         * @return  {string}
         */
        createdAt (entry) {
            return entry.created_at
                ? moment(
                    entry.created_at
                ).format(this.dateFormat)
                : null;
        },

        /**
         * Navigates to a different result page
         *
         * @param   {integer}  value
         * @return  {void}
         */
        goToPage(value) {
            this.navigateToPage({ value })
                .then(() => {
                    $(this.$el).find('.pagination li a').blur();
                });
        },

        /**
         * Updated at getter
         *
         * @param   {integer}  entry
         * @return  {string}
         */
        updatedAt (entry) {
            return entry.updated_at
                ? moment(
                    entry.updated_at
                ).format(this.dateFormat)
                : null;
        },

        generateLink (entry) {
            return `/admin/${this.selectedModule}/${entry.id}`;
        },
    },
};
