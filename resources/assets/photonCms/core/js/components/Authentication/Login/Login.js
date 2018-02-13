import {
    mapGetters,
    mapActions
} from 'vuex';

import {
    bindValidatorToForm,
    destroyValidator,
    processErrors,
    resetValidator
} from '_/services/formValidator';

const photonConfig = require('~/config/config.json');

export default {
    /**
     * Set the component's data storage
     *
     * @return  {void}
     */
    data: function() {
        return {
            credentials: {},
            rememberMe: true,
            redirectPath: photonConfig.startPage,
            validator: null,
            serverError: null,
            signUpEnabled: photonConfig.signUpEnabled,
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
            'clearAuthErrors',
            'login',
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
            if (this.$route.query.redirect) { // Set the redirect path if query parameter is present
                this.redirectPath = this.$route.query.redirect;
            }

            this.setBodyClass('login-page');

            this.setTitle('Login');

            $(this.$el).find('input[type="radio"], input[type="checkbox"]').uniform();

            this.validator = bindValidatorToForm({
                selector: '#login-form',

                onSubmit: () => {
                    this.clearAuthErrors();

                    this.login({
                        payload: this.credentials,
                        redirectPath: this.redirectPath
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

        $.uniform.restore('input[type="radio"], input[type="checkbox"]'); // Disabling uniform destroy since it flashes the native checkbox before changing route
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
