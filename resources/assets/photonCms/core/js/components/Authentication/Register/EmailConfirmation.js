import {
    mapGetters,
    mapActions
} from 'vuex';

import { errorTypes } from '_/services/errorTypes';

export default {
    /**
     * Set the component's data storage
     *
     * @return  {void}
     */
    data () {
        return {
            confirmationError: null,
            emailConfirmed: null,
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
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [ // Map actions from ui module namespace
            'setBodyClass',
            'setTitle',
        ]),

        ...mapActions('user', [ // Map actions from user module namespace
            'confirmEmail',
        ])
    },

    /**
     * Call a created hook
     *
     * @type  {function}
     * @return  void
     */
    created () {
        const emailToken = this.$route.params.emailToken;

        this.confirmEmail(emailToken)
            .then((message) => {
                if (message === 'USER_CONFIRMATION_SUCCESS') {
                    this.emailConfirmed = message;

                    return;
                }

                this.confirmationError = errorTypes[message] || message;
            });
    },

    /**
     * Call a mounted hook
     *
     * @type  {function}
     * @return  void
     */
    mounted () {
        this.$nextTick(() => {
            this.setBodyClass('login-page');

            this.setTitle('Email Confirmation');
        });
    }
};
