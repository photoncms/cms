import _ from 'lodash';

import { pWarn } from '_/helpers/logger';

import Vue from 'vue';

const DropZone = Vue.component(
        'DropZone',
        require('_/components/UserInterface/DropZone/DropZone.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        disabled: {
            type: Boolean,
        },
        id: {
            type: Number,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        refreshFields: {
            type: Number,
        },
        required: {
            type: Boolean,
        },
        value: {
            type: [File],
        },
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Set the components
     *
     * @return  {object}
     */
    components: {
        DropZone,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        /**
         * Create the admin getter based on the vuexModule prop
         *
         * @return  {object}
         */
        admin () {
            const getterName = `${this.vuexModule}/${this.vuexModule}`;

            return this.$store.getters[getterName];
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Emmits a change event with a payload consting of input value and the event object
         *
         * @param   {event}  event
         * @return  {void}
         */
        onChange: function(event) {
            this.$emit('change', {
                event,
                id: event.target.id,
                name: event.target.name,
                value: event.target.files[0],
            });
        },

        newFileUploaded () {
            return false;
        },
    },

    watch: {
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `InputTag.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                if(!this.value instanceof File) {
                    $(this.$el).find('.fileinput').fileinput('clear');
                }
            }
        },
    },
};
