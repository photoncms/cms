import _ from 'lodash';

import {
    mapGetters,
    mapActions,
} from 'vuex';

import { store } from '_/vuex/store';

import { router } from '_/router/router';

import {
    bindValidatorToForm,
    destroyValidator,
    processErrors,
    resetValidator,
} from '_/services/formValidator';

import { eventBus } from '_/helpers/eventBus';

import { updateValue } from '_/vuex/actions/formActions';

const ModuleFieldConfigurator = require('_/components/Generator/ModuleFieldConfigurator/ModuleFieldConfigurator.vue');

const ModuleReporter = require('_/components/Generator/ModuleReporter/ModuleReporter.vue');

import { getModuleOptions } from '_/components/Generator/ModuleConfigurator/ModuleConfigurator.moduleOptions';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            /**
             * Should table name auto formatting be allowed or not
             *
             * @type  {boolean}
             */
            autoFormattingTableName: false,

            /**
             * An object sent to child components; used to signal that the fields need to be reset, with the ability
             * to limit the reset to only certain fields
             *
             * @type  {Object}
             */
            formFieldsReset: {
                /**
                 * A list of field names to include in a reset action
                 *
                 * @type  {Array}
                 */
                includeFields: [],

                /**
                 * A parameter that is listened by the child components. Changed via moment().valueOf() method to always
                 * set the fresh value.
                 *
                 * @type  {integer}
                 */
                resetData: null,
            },

            /**
             * Module options object
             *
             * @type  {object}
             */
            moduleOptions: {},

            /**
             * Generated module report
             *
             * @type  {object}
             */
            moduleReport: null,

            /**
             * Server error
             *
             * @type  {[type]}
             */
            serverError: null,

            /**
             * jQuery Sortable instance reference
             *
             * @type  {object}
             */
            sortable: null,

            /**
             * Validator instance reference
             *
             * @type  {object}
             */
            validator: null,
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        ModuleFieldConfigurator,
        ModuleReporter,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            categoryModules: 'photonModule/categoryModules',
            generator: 'generator/generator',
            photonModules: 'photonModule/photonModules',
        }),

        /**
         * Informs if module has fields
         *
         * @return  {boolean}
         */
        moduleHasFields() {
            if(_.has(this.generator.selectedModule, 'fields') && !_.isEmpty(this.generator.selectedModule.fields)) {
                return true;
            }

            return false;
        },

        /**
         * Fetches the module name
         *
         * @return  {string}
         */
        moduleName () {
            if(_.has(this.generator.selectedModule, 'name') && this.generator.selectedModule.name) {
                return this.generator.selectedModule.name;
            }

            return this.$t('generator.myModuleName');
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('generator', [
            'clearGeneratorErrors',
            'createNewField',
            'deleteModule',
            'submitModuleForm',
            'updateFieldProperty',
            'updateFieldsOrder',
        ]),

        /**
         * The bindValidatorToForm wrapper method
         *
         * @return  {jQueryObject}
         */
        bindValidatorToFormHelper () {
            return bindValidatorToForm({
                selector: '#generator-form',

                onSubmit: () => {
                    this.clearErrors();

                    resetValidator(this.validator);

                    this.submitModuleForm({ reporting: true });
                }
            }, this);
        },

        /**
         * Clears the errors from the UI
         *
         * @return  {void}
         */
        clearErrors () {
            this.serverError = null;
        },

        /**
         * Used as callback for change event in ModuleConfigurator.moduleOptions.
         * Auto-fillin for table_name based on name being typed in.
         * This logic can possibly be moved to generator store since it mutates data.
         *
         * @return  {void}
         */
        createTableName () {
            if (this.autoFormattingTableName
                || !this.generator.selectedModule.table_name
                || this.generator.selectedModule.table_name === '') {

                this.autoFormattingTableName = true;

                const newTableName = this.generator.selectedModule['name']
                    .toLowerCase()
                    .replace(/-/g, ' ')
                    .split(' ')
                    .map(function(word) {
                        return word.replace(/\W/g, '');
                    })
                    .join('_');

                updateValue(
                    store,
                    'generator/UPDATE_GENERATOR_SELECTED_MODULE_TABLE_NAME',
                    null,
                    null,
                    newTableName
                );

                const updatedTableName = this.generator.selectedModule.table_name;

                this.moduleOptions[2].value = updatedTableName;
            }
        },

        /**
         * Used as callback for blur event in ModuleConfigurator.moduleOptions.
         * Finishes autoFormattingTableName session. Any further editing of name does not update the table_name
         *
         * @return  {void}
         */
        endModuleNameEdit () {
            if (this.autoFormattingTableName) {
                this.autoFormattingTableName = false;
            }
        },

        /**
         * Handles the file selection case by preselecting related module to assets and nullable to true
         *
         * @return  {void}
         */
        handleFileSelectionCase () {
            if (!_.has(this.generator.selectedModule, 'fields')) {
                return;
            }

            const arrayLength = this.generator.selectedModule.fields.length;

            for (var i = 0; i < arrayLength; i++) {
                const field = this.generator.selectedModule.fields[i];

                if (!(field.type == 15 || field.type == 16)) {
                    continue;
                }

                const assetsModule = _.find(this.photonModules, { table_name: 'assets'});

                let includeFields = [];

                this.updateFieldProperty({
                    id: `${field.id}|related_module`,
                    newValue: assetsModule.id
                })
                    .then(() => {
                        includeFields.push(`fields[${field.order}][related_module]`);

                        this.updateFieldProperty({
                            id: `${field.id}|nullable`,
                            newValue: true
                        })
                            .then(() => {
                                includeFields.push(`fields[${field.order}][nullable]`);

                                this.resetFormFields({ includeFields });
                            });
                    });
            }
        },

        /**
         * Toggles the resetFormFields data property in order to force refresh of the FormFields component
         *
         * @param   {object}  options.includeFields
         * @return  {void}
         */
        resetFormFields ({ includeFields = null} = {}) {
            this.formFieldsReset.resetData = moment().valueOf();

            this.formFieldsReset.includeFields = [];

            if(includeFields) {
                this.formFieldsReset.includeFields = includeFields;
            }
        },


        /**
         * Bind evenBus listeners
         *
         * @return  {[type]}  [description]
         */
        initEventBusListener () {
            eventBus.$on('fieldTypeBlur', payload => {
                if (payload.name === 'module[name]') {
                    this.endModuleNameEdit();
                }
            });

            eventBus.$on('fieldTypeChange', payload => {
                if (payload.name === 'module[name]') {
                    this.createTableName();
                }
            });
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

        /**
         * Toggles the visibility of additional options tab
         *
         * @param   {boolean}  options.show
         * @return  {void}
         */
        toggleAdditionalOptions({ show }) {
            if (show) {
                $('.additional-options').not('.in').collapse('show');

                return;
            }

            $('.additional-options').collapse('hide');
        },

        /**
         * Sort fields plugin setup
         *
         * @return  {void}
         */
        sortFields () {
            const $sortableFields = $(this.$el).find('.sortable-fields');

            if ($sortableFields.hasClass('ui-sortable')) {
                $sortableFields.sortable('destroy');
            }

            const updateFieldsOrder = this.updateFieldsOrder;

            this.sortable = $sortableFields.sortable({
                axis: 'y',
                cancel: '.placeholder, .flip-it',
                containment: 'parent',
                cursor: 'move',
                handle: '.sort-handle',
                items: '.sortable-item',
                opacity: 0.6,
                revert: false,
                scroll: true,
                scrollSensitivity: 50,
                tolerance: 'pointer',
                start() {
                    // Hide any tooltips on drag start
                    $('.tooltip').tooltip('hide');
                },
                stop() {
                    const newOrder = $sortableFields.sortable('toArray', { attribute: 'data-id' });

                    updateFieldsOrder({ newOrder });
                },
            });
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.initEventBusListener();

            $(this.$el).find('input[type="radio"], input[type="checkbox"]').uniform();

            this.validator = this.bindValidatorToFormHelper();

            this.moduleOptions = getModuleOptions(this);

            this.sortFields();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        $('.sortable-fields').sortable('destroy');

        this.sortable = false;

        destroyValidator(this.validator);
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        // If edited module is changed
        'generator.selectedModule.id' () {
            this.$nextTick(() => {
                this.moduleOptions = getModuleOptions(this);

                this.sortFields();
            });
        },

        'generator.refreshForm' () {
            this.$nextTick(() => {
                this.sortFields();
            });
        },

        'generator.selectedModule.fields': {
            deep: true,
            handler: function() {
                this.handleFileSelectionCase();

                this.sortFields();

                // $('.sortable-fields').sortable('refresh');

                // $('.sortable-fields').sortable('refreshPositions');

                destroyValidator(this.validator);

                this.clearErrors();

                this.validator = this.bindValidatorToFormHelper();
            },
        },

        'generator.error': {
            handler: function() {
                this.$nextTick(() => {
                    if (this.validator) {
                        resetValidator(this.validator);
                    }

                    // Show all hidden accordion panels
                    $('.panel-collapse').not('.in, .help-items-panel').collapse('show');

                    this.clearErrors();

                    this.serverError = processErrors(this.validator, this.generator.error);
                });
            },
            deep: true,
        },
    }
};
