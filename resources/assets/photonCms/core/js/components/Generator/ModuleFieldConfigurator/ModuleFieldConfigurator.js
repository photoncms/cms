import {
    mapGetters,
    mapActions,
} from 'vuex';

import { store } from '_/vuex/store';

import i18n from '_/i18n';

import { eventBus } from '_/helpers/eventBus';

import { updateValue } from '_/vuex/actions/formActions';

import { getModuleFieldOptions } from '_/components/Generator/ModuleFieldConfigurator/ModuleFieldConfigurator.moduleFieldOptions';

import { mapFromId } from '_/services/fieldTypes';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        formFieldsReset: {
            required: true,
            type: Object,
        },

        moduleField: {
            type: Object,
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            /**
             * Should field name auto formatting be allowed or not
             *
             * @type  {boolean}
             */
            autoFormattingFieldDbName: false,

            /**
             * Module field options object
             *
             * @type  {object}
             */
            moduleFieldOptions: {}
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            generator: 'generator/generator',
            photonModules: 'photonModule/photonModules',
        }),

        moduleFieldName () {
            if (this.moduleField.name) {
                const order = this.moduleField.order + 1;

                return `${order} ${this.moduleField.name}`;
            }

            return i18n.t('generator.myFieldName');
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('generator', [
            'deleteField',
        ]),

        /**
         * Bind evenBus listeners
         *
         * @return  {[type]}  [description]
         */
        initEventBusListener () {
            eventBus.$on('fieldTypeBlur', payload => {
                if (payload.name === `fields[${this.moduleField.order}][name]`) {
                    this.endFieldNameEdit();
                }
            });

            eventBus.$on('fieldTypeChange', payload => {
                switch (payload.name) {
                case `fields[${this.moduleField.order}][type]`:
                    this.refreshFieldOptions();

                    break;
                case `fields[${this.moduleField.order}][name]`:
                    this.onFieldNameEdit();

                    break;
                }
            });
        },

        /**
         * Used as callback for change event in ModuleFieldsConfigurator.moduleFieldOptions
         * Auto-fillin for db field name based on field name being typed in.
         * This logic can possibly be moved to generator store since it mutates data.
         *
         * return {void}
         */
        onFieldNameEdit () {
            // Gets name of the fields edited
            const fieldType = mapFromId[
                parseInt(this.moduleField.type, 10)
            ];

            let fieldDbNameProperty = 'column_name';

            let updateMutation = 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_COLUMN_NAME';

            if (fieldType.name === 'many-to-one' || fieldType.name === 'many-to-one') {
                fieldDbNameProperty = 'relation_name';

                updateMutation = 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_RELATION_NAME';
            }
            // TODO: cover the File(s) input field type here as well since it has a different
            // db name

            // Create unique field id by combining field id and db name property
            const fieldId = this.moduleField.id + '|' + fieldDbNameProperty;

            if (this.autoFormattingFieldDbName
                || !this.moduleField[fieldDbNameProperty]
                || this.moduleField[fieldDbNameProperty] === '') {

                this.autoFormattingFieldDbName = true;

                const newFieldDbNameValue = this.moduleField['name']
                    .toLowerCase()
                    .replace(/-/g, ' ')
                    .split(' ')
                    .map(function(word) {
                        return word.replace(/\W/g, '');
                    })
                    .join('_');

                updateValue(
                    store,
                    updateMutation,
                    fieldId,
                    `fields[${this.moduleField.id}][${fieldDbNameProperty}]`,
                    newFieldDbNameValue
                );

                const updatedFieldDbNameValue = this.generator.selectedModule.fields
                    .find(field => field.id === this.moduleField.id)[fieldDbNameProperty];

                for (var i = 0; i < this.moduleFieldOptions.length; i++) {
                    if (this.moduleFieldOptions[i].id === fieldId) {
                        this.moduleFieldOptions[i].value = updatedFieldDbNameValue;
                    }
                }
            }
        },

        /**
         * Used as callback for blur event in ModuleFieldsConfigurator.moduleFieldOptions.
         * Finishes autoFormattingTableName session. Any further editing of name
         * does not update the field's db name value
         *
         * return {void}
         */
        endFieldNameEdit () {
            if (this.autoFormattingFieldDbName) {
                this.autoFormattingFieldDbName = false;
            }
        },

        /**
         * Re-gets module field options defined in ModuleFieldsConfigurator.moduleFieldOptions
         *
         * @return  {void}
         */
        refreshFieldOptions () {
            this.moduleFieldOptions = getModuleFieldOptions(this);
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            this.initEventBusListener();

            this.refreshFieldOptions();
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'moduleField': {
            deep: true,
            handler: function() {
                this.refreshFieldOptions();
            },
        },
    }
};
