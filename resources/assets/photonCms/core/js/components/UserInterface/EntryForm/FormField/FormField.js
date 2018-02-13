import _ from 'lodash';

import { store } from '_/vuex/store';

import { updateValue } from '_/vuex/actions/formActions';

import { pWarn } from '_/helpers/logger';

import { mapGetters } from 'vuex';

import * as fieldComponents from '_/config/fieldTypeComponents';

import * as fieldComponentsDependencies from '~/config/fieldTypeComponents';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        canCreateSearchChoice: {
            type: Boolean,
        },
        defaultValue: {
            default: true,
            type: Boolean,
        },
        disabled: {
            type: Boolean,
        },
        fieldType: {
            required: true,
            type: String,
        },
        flattenToOptgroups: {
            type: Boolean,
        },
        formFieldsResetProp: {
            required: true,
            type: Object,
        },
        hidden: {
            default: false,
            type: Boolean,
        },
        id: {
            required: true,
            type: [
                Number,
                String,
            ]
        },
        inline: {
            default: false,
            type: Boolean,
        },
        isActiveEntryFilter: {
            default: null,
            type: String,
        },
        isSystem: {
            default: false,
            type: Boolean,
        },
        label: {
            default: null,
            type: String,
        },
        lazyLoading: {
            default: false,
            type: Boolean,
        },
        multiple: {
            default: false,
            type: Boolean,
        },
        name: {
            required: true,
            type: String,
        },
        optionsData: {
            type: Array,
        },
        placeholder: {
            type: String,
        },
        preloadDataConfig: {
            type: Object,
        },
        preselectFirst: {
            default: false,
            type: Boolean,
        },
        relatedTableName: {
            type: [
                Boolean,
                String,
            ]
        },
        mutation: {
            required: true,
            type: String,
        },
        required: {
            type: Boolean,
        },
        tooltip: {
            type: String,
        },
        value: {
            type: [
                Array,
                Boolean,
                File,
                Number,
                Object,
                String,
            ],
        },
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Define the components
     *
     * @type  {Object}
     */
    components: {
        ...fieldComponents,
        ...fieldComponentsDependencies,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
        }),

        fieldLabel () {
            return this.label ? this.label : this.placeholder;
        },
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data () {
        return {
            refreshFields: null
        };
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
         * @param   {integer}  options.id
         * @param   {string}  options.value
         * @return  {void}
         */
        onChange ({ id, value }) {
            updateValue(
                store,
                this.mutation,
                id,
                this.name,
                value
            );
        },

        /**
         * Toggles the resetRefreshFields data property in order to force refresh of the RefreshFields component
         *
         * @return  {void}
         */
        toggleResetRefreshFields () {
            this.refreshFields = moment().valueOf();
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            // Setup tooltip plugin if there's a tooltip field option set
            if (this.tooltip) {
                $(this.$el).find('[data-toggle="tooltip"]').tooltip({
                    'container': 'body'
                });
            }
        });
    },

    beforeDestroy: function() {
        // Destroy tooltip plugin if there was a tooltip field option set
        if (this.tooltip) {
            $(this.$el).find('[data-toggle="tooltip"]').tooltip('destroy');
        }
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        /**
         * Resets the entry form
         *
         * @param   {bool}  newEntry
         * @param   {bool}  oldEntry
         * @return  {void}
         */
        'formFieldsResetProp': {
            deep: true,
            handler (newEntry, oldEntry) {
                if(!_.isEmpty(newEntry.includeFields) && _.indexOf(newEntry.includeFields, this.name) === -1) {
                    return;
                }

                pWarn(
                    'Watched property fired.',
                    `FormField (${this.name})`,
                    'formFieldsResetProp',
                    newEntry,
                    oldEntry,
                    this
                );

                if (this.tooltip) {
                    $(this.$el).find('[data-toggle="tooltip"]').tooltip({
                        'container': 'body'
                    });
                }

                this.toggleResetRefreshFields();
            },
        },
    },
};
