import {
    mapGetters,
    mapActions,
} from 'vuex';

import i18n from '_/i18n';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            isLoading: false,
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            user: 'user/user',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('user', [
            'checkLicense',
        ]),

        /**
         * Force refreshes the license status
         *
         * @return  {void}
         */
        refreshStatus() {
            this.isLoading = true;

            this.checkLicense({ force: true })
                .then(() => {
                    this.isLoading = false;
                })
                .catch(() =>{
                    this.isLoading = false;
                });
        },
    },
};
