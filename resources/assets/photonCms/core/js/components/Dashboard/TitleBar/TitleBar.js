export default {
    /**
     * Set the methods
     * @type {Object}
     */
    methods: {
        /**
         * Titlebar events binder (mostly as inherited from Proton UI code)
         *
         * @return  {void}
         */
        bindEvents: function() {
            $(document).on('click', '.dashboard-menu', function(event) {
                event.preventDefault();

                $(this).toggleClass('expanded');

                $('.menu-state-icon').toggleClass('active');
            });
        },

        /**
         * Unbinds events
         *
         * @return  {void}
         */
        unbindEvents: function() {
            $(document).off('click', '.dashboard-menu');
        }
    },

    /**
     * Call a mounted hook
     *
     * @type {function}
     */
    mounted: function() {
        this.$nextTick(function() {
            this.bindEvents();
        });
    },

    /**
     * Call a beforeDestroy hook
     *
     * @type {function}
     */
    beforeDestroy: function() {
        this.unbindEvents();
    }
};
