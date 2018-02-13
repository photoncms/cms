import {
    mapGetters,
    mapActions
} from 'vuex';
import {
    pLog,
    pError,
} from '_/helpers/logger';

export default {
    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            notification: 'notification/notification',
        }),
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        // Map actions from admin module namespace
        ...mapActions('notification', [
            'showNotifications',
        ]),
    },

    watch: {
        'notification.notifications': function() {
            this.showNotifications()
                .then(function(response) {
                    pLog('Displaying the notification.', response);
                })
                .catch(function(response) {
                    pError('Failed to display the notification.', response);
                }
            );
        }
    }
};
