import _ from 'lodash';

import Vue from 'vue';

import { store } from '_/vuex/store';

const Select2 = Vue.component(
        'Select2',
        require('_/components/FieldTypes/Select2/Select2.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        advancedSearchName: {
            required: true,
            type: String,
        },
        disabled: {
            type: Boolean,
        },
        id: {
            required: true,
            type: Number,
        },
        label: {
            required: true,
            type: String,
        },
        name: {
            required: true,
            type: String,
        },
        refreshFields: {
            type: Number,
        },
        relatedTableName: {
            required: true,
            type: [
                Boolean,
                String,
            ]
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            /**
             * Toggles between the in and in_all filter method
             *
             * @type  {boolean}
             */
            useAnd: false,

            /**
             * Will refresh select2 when changed
             *
             * @type  {integer}
             */
            refresh: null,

            /**
             * Cached value used to transfer the value when changing the select2 name
             *
             * @type  {Array}
             */
            cachedValue: [],

            temporaryFieldValue: [],

            fieldValue: [],
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        Select2,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        /**
         * advancedSearch model getter
         *
         * @return  {object}
         */
        advancedSearch () {
            const getterName = `${this.advancedSearchName}/${this.advancedSearchName}`;

            return this.$store.getters[getterName];
        },

        /**
         * Sets the name used by the Vue advancedSearch module to deep nest the value in the filter object
         *
         * @return  {string}
         */
        select2Name () {
            return this.useAnd ? `filter.${this.name}.id.in_all` : `filter.${this.name}.id.in`;
        },

        /**
         * Gets the value
         *
         * @return  {string}
         */
        value () {
            const path = `payload.${this.select2Name}`;

            if (_.has(this.advancedSearch, path)) {
                return _.get(this.advancedSearch, path);
            }

            return null;
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Define a method that runs on change event
         *
         * @param   {string}  options.name
         * @param   {string}  options.value
         * @return  {void}
         */
        onChange ({ name, value }) {
            store.dispatch(`${this.advancedSearchName}/updateFilterValue`, { fieldName: name, value: value.join() })
                .then(()=>{
                    this.temporaryFieldValue = value;
                });
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.fieldValue = this.value;

        this.$nextTick(() => {
            $(this.$el).find('input[type="checkbox"]').uniform();
        });
    },

    /**
     * Set watched properties
     *
     * @type  {Object}
     */
    watch: {
        'useAnd' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.fieldValue = this.temporaryFieldValue;

                this.refresh = moment().valueOf();

                if (this.useAnd) {
                    const value = _.has(this.advancedSearch, `payload.filter.${this.name}.id.in`)
                        ? _.get(this.advancedSearch, `payload.filter.${this.name}.id.in`)
                        : '';

                    store.dispatch(`${this.advancedSearchName}/updateFilterValue`, { fieldName: this.select2Name, value })
                        .then(() => {
                            store.dispatch(`${this.advancedSearchName}/updateFilterValue`, { fieldName: `filter.${this.name}.id.in`, value: '' });
                        });

                    return;
                }

                const value = _.has(this.advancedSearch, `payload.filter.${this.name}.id.in_all`)
                    ? _.get(this.advancedSearch, `payload.filter.${this.name}.id.in_all`)
                    : '';

                store.dispatch(`${this.advancedSearchName}/updateFilterValue`, { fieldName: this.select2Name, value })
                    .then(() => {
                        store.dispatch(`${this.advancedSearchName}/updateFilterValue`, { fieldName: `filter.${this.name}.id.in_all`, value: '' });
                    });
            }
        },
    },
};
