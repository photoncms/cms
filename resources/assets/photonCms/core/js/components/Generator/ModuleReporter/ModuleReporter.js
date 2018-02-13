import {
    mapGetters,
    mapActions,
} from 'vuex';

import {
    destroyJsTree,
    setupJsTree
} from '_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.jsTree';

import { router } from '_/router/router';

export default {
    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            generator: 'generator/generator',
        }),

        /**
         * Gets the report data
         *
         * @return  {object}
         */
        report () {
            return this.generator.report;
        },

        /**
         * Gets the report type
         *
         * @return  {string}
         */
        reportType () {
            return this.generator.reportType;
        },

        /**
         * Gets the selected module data
         *
         * @return  {object}
         */
        selectedModule () {
            return this.generator.selectedModule;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('generator', [
            'clearReport',
            'deleteModule',
            'setGeneratorUi',
            'submitModuleForm',
            'updateRefreshForm',
        ]),

        ...mapActions('photonModule', [
            'getPhotonModules',
        ]),

        /**
         * Method to confirm and execute module changes
         * (submit to API without report flag)
         *
         * @return  {void}
         */
        confirmChange: function() {
            if (this.reportType === 'delete') {
                this.deleteModule({ reporting: false })
                    .then(() => {
                        this.getPhotonModules({ refreshList: true })
                            .then(() => {
                                this.setGeneratorUi();

                                router.push('/generator');

                                destroyJsTree();

                                setupJsTree(this);
                            });
                    });

                return;
            }

            // If this is a submit request, call submit module form action
            this.submitModuleForm({ reporting: false })
                .then((response) => {
                    const newModuleTableName = response.module.table_name;

                    const timestamp = moment().valueOf();

                    this.updateRefreshForm({ value: timestamp });

                    router.push(`/generator/${newModuleTableName}`);
                });
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            $('#modal-report').on('shown.bs.modal', function() {
                $('#modal-report .scrollable')[0].scrollTop = 0;
            });

            $('#modal-report').on('hidden.bs.modal', this.clearReport);
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        $('#modal-report').modal('hide');

        $('#modal-report').off('shown.bs.modal');

        $('#modal-report').off('hidden.bs.modal');
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'report': function() {
            if (this.report) {
                $('#modal-report').modal('show');
            }
        }
    }
};
