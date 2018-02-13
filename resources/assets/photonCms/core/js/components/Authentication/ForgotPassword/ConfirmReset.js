import {
    mapGetters,
    mapActions
} from 'vuex';

export default {
    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        ui: 'ui/ui'
    }),

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [ // Map actions from ui module namespace
            'setBodyClass',
            'setTitle'
        ])

    },

    /**
     * Call a mounted hook
     *
     * @type  {function}
     * @return  void
     */
    mounted: function() {
        this.$nextTick(function() {
            this.setBodyClass('login-page');

            this.setTitle('Password Reset Requested');
        });
    }

};
