import {
    mapGetters,
    mapActions
} from 'vuex';

// TODO: Refactor and implement menu items from API
export default {
    /**
     * Set the computed variables
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            ui: 'ui/ui',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [ // Map actions from photonModuleActions module namespace
            'getQuickLaunchMenu'
        ]),
    },

    /**
     * Call a mounted hook
     *
     * @type  {function}
     * @return  void
     */
    mounted: function() {
        this.$nextTick(function() {
            this.getQuickLaunchMenu();
        });
    },
};
