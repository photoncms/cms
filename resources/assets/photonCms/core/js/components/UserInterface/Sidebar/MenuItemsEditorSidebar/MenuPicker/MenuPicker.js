import { router } from '_/router/router';

import _ from 'lodash';

import { mapGetters } from 'vuex';

export default {
    /**
     * Set the component data
     *
     * @type  {Object}
     */
    data () {
        return {
            /**
             * All menus as returned by the API GET menu/ call
             *
             * @type  {Object}
             */
            allMenus: {},

            /**
             * Select2 plugin init options
             *
             * @type  {Object}
             */
            options: {},
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            menuItemsEditor: 'menuItemsEditor/menuItemsEditor',
        }),

        /**
         * Gets the menu description
         *
         * @return  {string}
         */
        menuDescription () {
            const selectedMenu = _.find(this.menuItemsEditor.allMenus, { 'id': parseInt(this.menuId) });

            if (!_.has(selectedMenu, 'description')) {
                return '';
            }

            return selectedMenu.description;
        },

        /**
         * Gets the menu id
         *
         * @return  {integer}
         */
        menuId () {
            return this.$route.params.menuId;
        }
    },

    methods: {
        /**
         * Initializes the Select2 plugin
         *
         * @return  {void}
         */
        initializeSelect2 () {
            this.options = {
                allowClear: false,
                language: {
                    noResults () {
                        return $('<li class="not-found">No matches found</li>');
                    }
                },
                placeholder: 'Select a Menu',
                theme: 'bootstrap',
            };

            this.initializeSelect2Plugin();
        },

        /**
         * Selects the DOM element and binds the Select2 plugin to it
         *
         * @return  {void}
         */
        initializeSelect2Plugin () {
            const $select2 = $(this.$el).find('select');

            const preloadedOptions = this.menuItemsEditor.allMenus.map((setting) => {
                return {
                    id: setting['id'],
                    text: setting['title'],
                };
            });

            this.options.data = preloadedOptions;

            $select2.select2(this.options);

            if(this.menuId) {
                $select2.val(this.menuId);

                $select2.trigger('change');
            }

            $select2.on('change', (event) => {
                router.push(`/menu-items-editor/${event.target.value}`);
            });
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.initializeSelect2();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        $(this.$el).find('select').select2('destroy');
    },
};
