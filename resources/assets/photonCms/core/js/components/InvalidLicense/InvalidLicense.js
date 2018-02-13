import { mapActions } from 'vuex';

import { router } from '_/router/router';

import i18n from '_/i18n';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            error: {
                info: i18n.t('license.licenseKeyProblemExplanation'),
                title: i18n.t('license.licenseKeyProblem'),
            },
            isLoading: false,
        };
    },

    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: {
        getActionButtonLink () {
            if (this.$route.params.errorType === 'license-key-domain-inactive') {
                return 'https://photoncms.com/pricing';
            }

            return 'https://photoncms.com/support';
        },
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
            'checkLicense',
        ]),

        /**
         * Bootstraps the component
         *
         * @param   {string}  errorType
         * @return  {void}
         */
        bootstrap (errorType) {
            if (errorType === 'license-key-inactive') {
                this.error.title = i18n.t('license.licenseKeyInactive');
                this.error.info = i18n.t('license.licenseKeyInactiveExplanation');
            }

            if (errorType === 'license-key-domain-inactive') {
                this.error.title = i18n.t('license.licenseKeyDomainInactive');
                this.error.info = i18n.t('license.licenseKeyDomainInactiveExplanation');
            }

            this.setBodyClass('dashboard-page');

            this.setTitle(this.error.title);
        },

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

                    return router.push('/');
                })
                .catch((response) =>{
                    this.isLoading = false;

                    if (response === 'PHOTON_LICENSE_KEY_INACTIVE') {
                        return router.push('/invalid-license/license-key-inactive');
                    }

                    if (response === 'PHOTON_LICENSE_KEY_DOMAIN_INACTIVE') {
                        return router.push('/invalid-license/license-key-domain-inactive');
                    }

                    return router.push('/invalid-license');
                });
        },
    },

    /**
     * Call a mounted hook
     *
     * @type  {function}
     * @return  void
     */
    mounted: function() {
        this.$nextTick(function() {
            const errorType = this.$route.params.errorType;

            this.bootstrap (errorType);
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        /**
         * Watching the route change
         *
         * @return  {void}
         */
        $route: {
            deep: true,
            handler (newEntry) {
                const errorType = newEntry.params.errorType;

                this.bootstrap (errorType);
            },
        },

    },
};
