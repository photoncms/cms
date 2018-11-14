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
            photonModules: 'photonModule/photonModules',
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
         * Fetches the access permission for a given moduel
         *
         * @param   {object}  resourceData
         * @return  {boolean}
         */
        canAccess (resourceData) {
            if(!_.has(resourceData, 'table_name')) {
                return true;
            }

            const moduleData = _.find(this.photonModules, [ 'table_name', resourceData.table_name ]);

            return _.has(moduleData, 'permission_control.access') ? moduleData.permission_control.access : true;
        },

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
