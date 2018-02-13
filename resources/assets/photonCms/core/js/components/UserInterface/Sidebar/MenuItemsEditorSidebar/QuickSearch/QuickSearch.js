import { router } from '_/router/router';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        nodes: {
            type: Array,
        },
    },

    /**
     * Set the component data
     *
     * @type  {Object}
     */
    data: function() {
        return {
            // Select2 plugin initialization options
            options: {},
        };
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
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
                    noResults: function() {
                        return $('<li class="not-found">No matches found</li>');
                    }
                },
                placeholder: this.placeholder,
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


            const preloadedOptions = this.nodes.map((setting) => {
                return {
                    id: setting['id'],
                    text: setting['text'],
                };
            });

            this.options.data = preloadedOptions;

            $select2.select2(this.options);

            $select2.on('change', (event) => {
                const menuId = router.currentRoute.params.menuId;

                router.push(`/menu-items-editor/${menuId}/${event.target.value}`);
            });
        },
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        const $select2 = $(this.$el).find('select');

        if ($($select2).data('select2')) {
            $select2.select2('destroy');

            $select2.empty();

            $select2.off();
        }
    },

    /**
     * Set watched properties
     *
     * @type  {Object}
     */
    watch: {
        'nodes' () {
            const $select2 = $(this.$el).find('select');

            if ($($select2).data('select2')) {
                $select2.select2('destroy');

                $select2.empty();

                $select2.off();
            }

            this.initializeSelect2();
        },
    },
};
