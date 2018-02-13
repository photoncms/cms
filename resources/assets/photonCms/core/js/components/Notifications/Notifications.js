import {
    mapGetters,
    mapActions
} from 'vuex';

import { store } from '_/vuex/store';

import { pError } from '_/helpers/logger';

import { config } from '_/config/config';

import {
    Assets,
    Breadcrumb,
    LicenseExpiringNotification,
    MainMenu,
    TitleBlock,
    UserMenu,
} from '@/components';

import i18n from '_/i18n';

export default {
    /**
     * Set the components
     *
     * @type  {object}
     */
    components: {
        Assets,
        Breadcrumb,
        LicenseExpiringNotification,
        MainMenu,
        TitleBlock,
        UserMenu,
    },

    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        notifications: 'notification/notification',
        ui: 'ui/ui'
    }),

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Performs the admin cleanup
         *
         * @param   {function}  options.commit
         * @return  {void}
         */
        adminCleanup: ({commit}) => commit('ADMIN_CLEANUP'),

        /**
         * Returns the breadcrumbs array
         *
         * @return  {array}
         */
        breadcrumbItems () {
            return [{
                text: 'Notifications',
                link: 'notifications'
            }];
        },

        /**
         * Returns the from now formatted time
         *
         * @param   {string}  dateTime
         * @return  {string}
         */
        fromNow (dateTime) {
            return moment(dateTime).fromNow(true);
        },

        ...mapActions('ui', [
            'animateWrapper',
            'setBodyClass',
            'setBreadcrumbItems',
            'setTitle',
        ]),

        ...mapActions('notification', [
            'getAllNotifications',
            'markAllNotificationsAsRead',
            'readNotification',
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
            this.getAllNotifications(store)
                .catch((error) => {
                    if (config.ENV.debug) {
                        pError(error);
                    }
                });

            this.setBreadcrumbItems(this.breadcrumbItems());

            this.setBodyClass('admin');

            this.setTitle(i18n.t('notifications.notifications'));

            this.animateWrapper();
        });
    },

    /**
     * Set the watched expressions
     *
     * @type  {Object}
     */
    watch: {
        'ui.notificationsBadge': function() {
            this.$nextTick(() => {
                this.getAllNotifications(store);
            });
        }
    },

    /**
     * Call a beforeDestroy hook
     *
     * @type  {function}
     * @return  void
     */
    beforeDestroy: function() {
        this.adminCleanup();

        this.setSidebarType(null);
    }
};
