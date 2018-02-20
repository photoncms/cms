import {
    mapGetters,
    mapActions
} from 'vuex';

import { userHasRole } from '_/vuex/actions/userActions';

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

        /**
         * Checks if a user has given role
         *
         * @param   {string}  role
         * @return  {bool}
         */
        userHasRole,
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
