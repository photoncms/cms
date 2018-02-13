import { mapActions } from 'vuex';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        userId: {
            type: Number,
            required: true,
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        // Map actions from admin module namespace
        ...mapActions('user', [
            'impersonateUser'
        ]),

        impersonate: function impersonate(id) {
            this.impersonateUser({ id });
        }
    },

};
