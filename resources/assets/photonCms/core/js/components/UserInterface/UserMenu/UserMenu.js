import Vue from 'vue';
import {
    mapGetters,
    mapActions
} from 'vuex';

const NotificationsMenu = Vue.component('NotificationsMenu', require('_/components/Notifications/NotificationsMenu.vue'));

import { router } from '_/router/router';

export default {
    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        notifications: 'notification/notification',
        ui: 'ui/ui',
        user: 'user/user',
    }),

    /**
     * Define the components used
     *
     * @type  {Object}
     */
    components: {
        NotificationsMenu,
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('notification', [
            'markAllNotificationsAsRead',
        ]),

        markAsRead () {
            this.markAllNotificationsAsRead()
                .then(() => {
                    this.toggleNotificationsPanel();
                });
        },

        seeAll() {
            setTimeout(function() {
                $('.notifications-view').fade(60);
            }, 60);

            router.push('/notifications');
        },

        /**
         * Legacy shuffle DOM elements method inherited from Proton UI
         * TODO: Needs to be refactored
         *
         * @return  {void}
         */
        shuffleUserNav: function() {
            if (Modernizr.mq('(min-width:' + (this.ui.screenXs) + 'px)')) {
                $('body > .user-menu').prependTo('.wrapper');
            } else {
                if ($('.user-menu').length > 1) {
                    $('body > .user-menu').remove();
                }

                $('.wrapper .user-menu').prependTo('body');
            }
        },

        /**
         * Returns a fromNow formatted time
         *
         * @param   {string}  dateTime
         * @return  {string}
         */
        fromNow: function(dateTime) {
            return moment(dateTime).fromNow(true);
        },

        /**
         * Toggles a notifications panel
         *
         * @return  {void}
         */
        toggleNotificationsPanel: function() {
            setTimeout(function() {
                $('.notifications-view').fadeToggle(60);
            }, 60);
        },

        /**
         * Animates a bouncer
         *
         * @return  {void}
         */
        bounceCounter: function() {
            if (!$('.menu-counter').length) {
                return;
            }

            $('.menu-counter').toggleClass('animated bounce');

            setTimeout(() => {
                $('.menu-counter').toggleClass('animated bounce');
            }, 1000);

            setTimeout(() => {
                this.bounceCounter();
            }, 5000);
        },

        /**
         * Map actions from ui module namespace
         */
        ...mapActions('notification', [
            'readNotification'
        ])
    },

    mounted: function() {
        this.$nextTick(function() {
            this.shuffleUserNav(); // Shuffle on menu ready

            this.bounceCounter();
        });
    },

    watch: {
        'ui.bodyWidth': function() {
            this.shuffleUserNav(); // Shuffle on resize
        }
    }
};
