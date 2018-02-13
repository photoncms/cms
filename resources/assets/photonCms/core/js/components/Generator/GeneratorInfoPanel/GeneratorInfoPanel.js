import _ from 'lodash';

import { router } from '_/router/router';

import Vue from 'vue';

import {
    mapActions,
    mapGetters
} from 'vuex';

import { store } from '_/vuex/store';

const InputTextField = Vue.component(
        'InputTextField',
        require('_/components/FieldTypes/InputTag/InputTag.vue')
    );

export default {
    /**
     * Set the components
     *
     * @type  {Object}
     */
    components: {
        InputTextField,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            generator: 'generator/generator',
            photonModule: 'photonModule/photonModule',
            subscription: 'subscription/subscription',
            ui: 'ui/ui',
        }),

        /**
         * Date format getter
         *
         * @return  {string}
         */
        dateFormat () {
            return this.ui.photonConfig.dateFormat;
        },

        /**
         * Created at getter
         *
         * @return  {string}
         */
        createdAt () {
            return this.generator.selectedModule.created_at
                ? moment(
                    this.generator.selectedModule.created_at
                ).format(this.dateFormat)
                : null;
        },

        /**
         * Updated at getter
         *
         * @return  {string}
         */
        updatedAt () {
            return this.generator.selectedModule.updated_at
                ? moment(
                    this.generator.selectedModule.updated_at
                ).format(this.dateFormat)
                : null;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Triggers the create mode
         *
         * @return  {void}
         */
        createNewModule () {
            router.push('/generator');
        },

        /**
         * Reroutes to module editing
         *
         * @param   {string}  tableName
         * @return  {void}
         */
        navigateToModule(tableName) {
            router.push(`/admin/${tableName}`);
        },
    },
};
