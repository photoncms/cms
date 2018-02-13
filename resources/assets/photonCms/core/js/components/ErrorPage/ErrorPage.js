import {
    mapGetters,
    mapActions
} from 'vuex';

import { store } from '_/vuex/store';

export default {
    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: {
        ...mapGetters({
            error: 'error/error'
        }),

        invalidToken: function() {
            return this.error.errorCode === '401';
        },
    },

    /**
     * Set the beforeRouteEnter hook
     *
     * @return  {void}
     */
    beforeRouteEnter: function (to, from, next) {
        if (!to.params || !to.params.errorName) {
            store.dispatch('error/set404State');
        }

        // If api not responding return 503 error page
        if (to.params.errorName === 'api-not-accessible') {
            store.dispatch('error/setError',
                {
                    errorCode: '503',
                    errorIcon: 'fa fa-frown',
                    errorText: 'API Service Unavailable.',
                });
        }

        // If the token is invalid return 401 error page
        if (to.params.errorName === 'invalid-token') {
            store.dispatch('error/setError', {
                errorCode: '401',
                errorIcon: 'fa fa-frown',
                errorText: 'Invalid Token. Not logged in.',
            });
        }

        next();
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        // Map actions from ui module namespace
        ...mapActions('ui', [
            'setBodyClass',
            'setTitle'
        ]),

        // Map actions from error module namespace
        ...mapActions('error', [
            'set404State',
            'setError',
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
            this.setBodyClass('dashboard-page');

            this.setTitle('There\'s been an error.');
        });
    }
};
