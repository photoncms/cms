import {
    mapActions,
    mapGetters,
} from 'vuex';

export default {
    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
        }),
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('admin', [
            'extensionCall'
        ]),
    },
};
