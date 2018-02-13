import {
    mapGetters,
    mapActions
} from 'vuex';

import { errorTypes } from '_/services/errorTypes';

export default {
    data () {
        return {
            confirmationError: false,
            emailConfirmed: false,
        };
    },

    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        ui: 'ui/ui',
        user: 'user/user',
    }),

    /**
     * Call a created hook
     *
     * @type  {function}
     * @return  void
     */
    created () {
        const emailToken = this.$route.params.emailToken;

        const userId = this.$route.params.userId;

        this.confirmEmailChange({ emailToken, userId })
            .then((message) => {
                if (message === 'EMAIL_ADDRESS_CHANGED') {
                    this.emailConfirmed = message;

                    this.removeSessionData();

                    return;
                }

                this.confirmationError = errorTypes[message] || message;
            });
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [
            'setBodyClass',
            'setTitle'
        ]),

        ...mapActions('user', [
            'confirmEmailChange',
            'removeSessionData',
        ])
    },

    mounted () {
        this.$nextTick(() => {
            this.setBodyClass('login-page');

            this.setTitle('Email Confirmation');
        });
    }
};
