import {
    mapGetters,
    mapActions
} from 'vuex';
import {
    bindValidatorToForm,
    resetValidator,
    destroyValidator
} from '_/services/formValidator';
import { router } from '_/router/router';
import { errorTypes } from '_/services/errorTypes';

export default {
    /**
     * Set the component's data storage
     *
     * @return  {void}
     */
    data: function() {
        return {
            credentials: { },
            validator: null,
            serverError: null
        };
    },

    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        ui: 'ui/ui',
        user: 'user/user'
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
        ]),

        ...mapActions('user', [ // Map actions from user module namespace
            'resetPassword'
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

            this.setTitle('Set New Password');

            this.validator = bindValidatorToForm({
                selector: '#reset-password-form',

                onSubmit: () => {
                    this.credentials.token = this.$route.params.emailToken;

                    this.resetPassword(this.credentials)
                        .then((message) => {
                            if (message === 'PASSWORD_RESET_SUCCESS') {
                                router.push('/password-reset-success');

                                return;
                            }

                            resetValidator(this.validator);

                            this.serverError = errorTypes[message] || message;
                        });
                }
            }, this);
        });
    },

    /**
     * Call a beforeDestroy hook
     *
     * @type  {function}
     * @return  void
     */
    beforeDestroy: function() {
        destroyValidator(this.validator);
    }
};
