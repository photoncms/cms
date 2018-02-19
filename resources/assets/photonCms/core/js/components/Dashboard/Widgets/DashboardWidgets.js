import Vue from 'vue';

import {
    mapGetters,
    mapActions,
} from 'vuex';

import { userHasRole } from '_/vuex/actions/userActions';

const LatestEntriesWidget = Vue.component(
        'LatestEntriesWidget',
        require('_/components/Dashboard/Widgets/LatestEntriesWidget/LatestEntriesWidget.vue')
    );

export default {
    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        LatestEntriesWidget,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            ui: 'ui/ui',
            user: 'user/user',
            widget: 'widget/widget',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('widget', [
            'createNewWidget',
            'getWidgetModuleFields',
            'getWidgets',
            'resetWidgetModel',
        ]),

        /**
         * Creates a special animation effect
         *
         * @param   {boolean}  progressive
         * @return  {void}
         */
        lightUp (progressive) {
            const time = progressive ? 200 : 0;

            $('.proton-widget').map((index, widget) => {
                setTimeout(() => {
                    $(widget).addClass('lit');
                }, time * index);
            });
        },

        /**
         * Binds the sortable to widgets
         *
         * @return  {void}
         */
        sortWidgets () {
            $('.widget-group').sortable({
                cancel: '.placeholder, .flip-it',
                items: '.proton-widget, .modal',
                placeholder: 'drag-placeholder',
                start () {
                    $('.tooltip').tooltip('hide');
                    $('#new-widget-modal').modal('hide');
                },
                stop () {
                    // proton.dashboard.saveWidgetPositions();
                },
                tolerance: 'pointer',
                handle: '.panel-heading'
            });
        },

        /**
         * Creates a nw widget
         *
         * @param   {string}  type
         * @return  {void}
         */
        newWidget (type) {
            this.createNewWidget({ type })
                .then(() => {
                    $('#new-widget-modal').modal('hide');

                    setTimeout(() => {
                        this.lightUp();

                        $('.proton-widget').last().addClass('animated pulse');

                        setTimeout(() => {
                            $('.proton-widget.animated').removeClass('animated pulse');
                        }, 2000);

                        $('.widget-group').sortable('refresh');
                    });
                });
        },

        /**
         * Checks if a user has given role
         *
         * @param   {string}  role
         * @return  {bool}
         */
        userHasRole,
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.getWidgetModuleFields()
                .then(() => {
                    this.getWidgets()
                        .then(() => {
                            this.lightUp(true);

                            this.sortWidgets();
                        });
                });
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        this.resetWidgetModel();

        $('.widget-group').sortable('destroy');

        $('#new-widget-modal').modal('hide');
    }
};
