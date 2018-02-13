import { config } from '_/config/config';

import { router } from '_/router/router';

import { storage } from '_/services/storage';

const photonConfig = require('~/config/config.json');

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        /**
         * An array of fields for the selected module
         */
        selectedModule: {
            required: true,
            type: Object,
        },
    },

    /**
     * Set the component data
     *
     * @type  {Object}
     */
    data: function() {
        return {
            /**
             * License Status object
             *
             * @type  {object}
             */
            licenseStatus: storage.get('licenseStatus', true),

            // Select2 plugin initialization options
            options: {},
        };
    },

    /**
     * Set the computed properties
     *
     * @type  {Object}
     */
    computed: {
        /**
         * Sets the active state for the advanced search button
         *
         * @return  {boolean}
         */
        activeSearch () {
            return this.$route.params.moduleEntryId === 'search';
        },

        disabledAdvancedSearch () {
            return !(this.licenseStatus.domainType === 1 || this.licenseStatus.licenseType === 4);
        },

        /**
         * Sets the disabled state for the search input
         *
         * @return  {boolean}
         */
        disabledSearch () {
            return (this.$route.params.moduleEntryId === 'search' || this.selectedModule.type == 'single_entry');
        },

        /**
         * Gets the module table name
         *
         * @return  {string}
         */
        relatedTableName () {
            return this.$route.params.moduleTableName;
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Navigates to the search page
         *
         * @return  {void}
         */
        enterSearchMode: function() {
            if (this.selectedModule.type == 'single_entry') {
                return false;
            }

            if (this.$route.params.moduleEntryId === 'search') {
                router.push(`/admin/${this.relatedTableName}`);

                return;
            }

            router.push(`/admin/${this.relatedTableName}/search`);
        },

        /**
         * Initializes the Select2 plugin
         *
         * @return  {void}
         */
        initializeSelect2: function() {
            this.options = {
                allowClear: false,
                language: {
                    noResults: function() {
                        return $('<li class="not-found">No matches found</li>');
                    }
                },
                placeholder: this.placeholder,
                theme: 'bootstrap',
            };

            this.setAjaxOptions();

            this.initializeSelect2Plugin();
        },

        /**
         * Selects the DOM element and binds the Select2 plugin to it
         *
         * @return  {void}
         */
        initializeSelect2Plugin: function() {
            const $select2 = $(this.$el).find('select');

            $select2.select2(this.options);

            $select2.on('change', (event) => {
                router.push(`/admin/${this.relatedTableName}/${event.target.value}`);
            });
        },

        setAjaxOptions: function() {
            const apiToken = storage.get('apiToken');

            const itemsPerPage = photonConfig.paginatedNodesItemsPerPage;

            this.options.ajax = {
                cache: true,
                data: function(term) {
                    let payload = {
                        filter: {
                            anchor_text: {
                                like: term.term,
                            },
                        },
                        include_relations: false,
                        pagination: {
                            current_page: term.page || 1,
                            items_per_page: itemsPerPage
                        }
                    };

                    return payload;
                },
                dataType: 'json',
                delay: 250,
                headers: {
                    Authorization: `Bearer ${apiToken}`
                },
                processResults: function(data) {
                    return {
                        pagination: {
                            more: data.body.pagination.has_more_pages,
                        },
                        results: data.body.entries
                    };
                },
                results: function(data) {
                    return {
                        more: true,
                        results: data.body.entries,
                    };
                },
                type: 'POST',
                url: config.ENV.apiBasePath + '/filter/' + this.relatedTableName,
            };

            this.options.minimumInputLength = 2;

            this.options.templateResult = function(data) {
                return data.anchor_text;
            };

            this.options.templateSelection = function(data) {
                if (data.anchor_text) {
                    return data.anchor_text;
                }

                return data.text;
            };
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            this.initializeSelect2();
        });
    },

    beforeDestroy: function() {
        const $select2 = $(this.$el).find('select');

        if ($($select2).data('select2')) {
            $select2.select2('destroy');
        }
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        $route (newEntry, oldEntry) {
            const moduleTableName = newEntry.params.moduleTableName;

            const oldModuleTableName = oldEntry.params.moduleTableName;

            // If module has changed
            if (moduleTableName !== oldModuleTableName) {
                const $select2 = $(this.$el).find('select');

                if ($($select2).data('select2')) {
                    $select2.select2('destroy');
                }

                this.initializeSelect2();
            }
        },
    },
};
