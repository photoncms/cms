import {
    mapGetters,
    mapActions
} from 'vuex';
import {
    bindValidatorToForm,
    resetValidator,
    destroyValidator,
    processErrors
} from '_/services/formValidator';

export default {
    /**
     * Set the component's data storage
     *
     * @return  {void}
     */
    data: function() {
        return {
            invitationToken: this.$route.params.invitationToken,
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
            'clearAuthErrors',
            'register'
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

            this.setTitle('Register');

            this.validator = bindValidatorToForm({
                selector: '#registration-form',

                onSubmit: () => {
                    this.clearAuthErrors();

                    this.register({
                        payload: this.credentials,
                        invitationToken: this.invitationToken
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
        this.clearAuthErrors();

        destroyValidator(this.validator);
    },

    /**
     * Set the watched expressions
     *
     * @type  {Object}
     */
    watch: {
        'user.error.message': function() {
            this.$nextTick(() => {
                resetValidator(this.validator);

                this.serverError = processErrors(this.validator, this.user.error);
            });
        }
    }
};
