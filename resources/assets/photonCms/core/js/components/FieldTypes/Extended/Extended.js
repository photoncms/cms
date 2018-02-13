import _ from 'lodash';

import { store } from '_/vuex/store';

import { api } from '_/services/api';

import { mapGetters } from 'vuex';

import { config } from '_/config/config';

import { updateValue } from '_/vuex/actions/formActions';

import {
    pLog,
    pWarn,
    pError
} from '_/helpers/logger';

import { getEntryFields } from '_/components/Admin/Admin.fields';

import { EntryForm } from '@/components';

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
        fieldType: {
            type: String,
            required: true,
        },
        id: {
            required: true,
            type: [
                Number,
                String,
            ],
        },
        multiple: {
            type: Boolean,
        },
        name: {
            required: true,
            type: String,
        },
        placeholder: {
            type: String,
        },
        preloadDataConfig: {
            default () {
                return {
                    method: 'post',
                    payload: {
                        include_relations: false,
                    },
                    resultsObjectPath: 'body.body.entries',
                    valuesOfInterest: {
                        id: 'id',
                        text: 'anchor_text',
                    },
                    url: false,
                };
            },
            type: Object
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
        value: {
            default: null,
            type: [
                Array,
                Number,
                Object
            ],
        },
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            /**
             * The id of the item that is in the predeletion state
             *
             * @type  {integer}
             */
            confirmDelete: null,

            /**
             * The id of an option currently edited
             *
             * @type  {[type]}
             */
            editedOptionId: null,

            /**
             * Stores the entry fields configuration object
             *
             * @type  {Array}
             */
            fields: [],

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
             * Entry form visibility state
             *
             * @type  {boolean}
             */
            formVisible: false,

            /**
             * Stores the data object of currently selected options
             *
             * @type  {Array}
             */
            options: [],
        };
    },

    /**
     * Set the components
     *
     * @return  {object}
     */
    components: {
        EntryFormInline: EntryForm,
    },

    /**
     * Set the computed properties
     *
     * @type  {object}
     */
    computed: {
        // Map getters
        ...mapGetters({
            assetsManager: 'assetsManager/assetsManager',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        }),

        /**
         * Define the admin getter
         *
         * @return  {object}
         */
        admin () {
            const getterName = `${this.registeredModuleName}/${this.registeredModuleName}`;

            return this.$store.getters[getterName];
        },

        /**
         * Used to disable the 'add option' button for OneToManyExtended field type
         *
         * @return  {Boolean}  [description]
         */
        isOneToMany () {
            return (this.fieldType === 'OneToManyExtended' && this.parentEntryAdminModule.editorMode !== 'edit');
        },

        /**
         * The getter for parrent module
         *
         * @return  {object}
         */
        parentEntryAdminModule () {
            const getterName = `${this.vuexModule}/${this.vuexModule}`;

            return this.$store.getters[getterName];
        },

        /**
         * Returns an array of IDs of currently selected objects
         *
         * @return  {array}
         */
        selectedOptionsIds () {
            if (_.isEmpty(this.options)) {
                return [];
            }

            return this.options.map(option => {
                return option.id;
            });
        },

        /**
         * Makes sure that value returned is always an array of values mapped
         * by the preloadDataConfig.valuesOfInterest.id parameter
         *
         * @return  {array}
         */
        values () {
            if(!this.value) {
                return this.value;
            }

            if (Array.isArray(this.value)) {
                const onlyIdsOfInterest = this.value.map((value) => {
                    if(_.isObject(value)) {
                        return [_.get(value, this.preloadDataConfig.valuesOfInterest.id)];
                    }

                    return [value];
                });

                return onlyIdsOfInterest;
            }

            if(_.isObject(this.value)) {
                return [_.get(this.value, this.preloadDataConfig.valuesOfInterest.id)];
            }

            return [this.value];
        },

        /**
         * Gets the registeredModuleName
         *
         * @return  {string}
         */
        registeredModuleName () {
            return `extended-${this.name}-${this.id}`;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Dispatches the confirmDeleteEntry action
         *
         * @param   {integer}  optionId
         * @return  {void}
         */
        confirmDeleteEntry (optionId) {
            if (!this.confirmDelete) {
                this.confirmDelete = optionId;
            } else {
                this.confirmDelete = null;
            }

            return store.dispatch(`${this.registeredModuleName}/confirmDeleteEntry`);
        },

        /**
         * Launches the form editor to edit the selected option
         *
         * @param   {integer}  optionId
         * @return  {void}
         */
        editOption (optionId) {
            this.formBootstrap(optionId);

            this.editedOptionId = optionId;
        },

        /**
         * Emits the change event
         *
         * @return  {void}
         */
        emitChange () {
            //For OneToManyExtended field relation is stored in the remote module
            if(this.fieldType === 'OneToManyExtended') {
                return;
            }

            let selection = null;

            if (!_.isEmpty(this.selectedOptionsIds)) {
                selection = (this.multiple)
                    ? this.selectedOptionsIds
                    : this.selectedOptionsIds[0];
            }

            this.$emit('change', {
                event,
                id: this.id,
                name: this.name,
                value: selection,
            });
        },

        /**
         * SHould the deletion confirmation be shown
         *
         * @param   {integer}  optionId
         * @return  {Boolean}
         */
        isConfirmEntryVisible(optionId) {
            return this.confirmDelete === optionId;
        },

        /**
         * Bootstraps the form
         *
         * @param   {integer}  moduleEntryId
         * @return  {[type]}  [description]
         */
        formBootstrap (moduleEntryId = null) {
            store.dispatch(`${this.registeredModuleName}/adminBootstrap`, {
                moduleEntryId,
                moduleTableName: this.relatedTableName,
                shouldFetchSearch: false,
            })
                .then(() => {
                    this.setFormVisibility(true);

                    this.fields = getEntryFields(this);

                    this.resetFormFields();

                    /**
                     * Only for OneToManyExtended field type we need to be send the parent entry id before creating
                     * the child entry.
                     */
                    if(this.fieldType === 'OneToManyExtended') {
                        /**
                         * Locate the field that hold the relation to the current module
                         */
                        const moduleTableName = this.parentEntryAdminModule.selectedModule.table_name;

                        const moduleEntryId = !isNaN(this.parentEntryAdminModule.editedEntry.id)
                            ? this.parentEntryAdminModule.editedEntry.id
                            : null;

                        const relationField = _.find(this.fields, { relatedTableName: moduleTableName});

                        if (moduleEntryId) {
                            updateValue(
                                store,
                                relationField.mutation,
                                relationField.id,
                                relationField.name,
                                moduleEntryId
                            );
                        }
                    }
                })
                .catch((error) => {
                    pError('adminBootstrap action failed with an error', error);
                });
        },

        hideForm () {
            this.setFormVisibility(false);

            this.editedOptionId = null;
        },

        /**
         * Loads initial values from the API
         *
         * @param   {object}  $select2  Select2 DOM element
         * @return  {void}
         */
        loadInitialValuesFromAPI () {
            pLog('Extdended Field loadInitialValuesFromAPI (name, values)', this.name, this.values, this.relatedTableName);

            if (!this.relatedTableName) {
                return;
            }

            let initialSelectionPayload = {
                filter: {
                    id: {
                        in: this.values,
                    }
                },
                include_relations: true,
            };

            api.post(config.ENV.apiBasePath + '/filter/' + this.relatedTableName, initialSelectionPayload)
                .then((response) => {
                    this.options =  _.get(response, this.preloadDataConfig.resultsObjectPath, []);
                })
                .catch((response) => {
                    pError(`Failed to load initial options for Extended component #${this.id}`, response);
                });
        },

        /**
         * Sets the formFieldsReset data property in order to force refresh of the EntryForm component
         *
         * @param   {object}  options.includeFields The names of field components that need to be included in reset
         * @return  {void}
         */
        resetFormFields ({ includeFields = null } = {}) {
            this.formFieldsReset.resetData = moment().valueOf();

            this.formFieldsReset.includeFields = [];

            if(includeFields) {
                this.formFieldsReset.includeFields = includeFields;
            }
        },

        /**
         * Removes an option from selected options
         *
         * @param   {integer}  optionId
         * @return  {void}
         */
        removeOption (optionId) {
            if(this.fieldType === 'OneToManyExtended') {
                store.dispatch(`${this.registeredModuleName}/deleteEntry`, {
                    entryId: optionId,
                    selectedModuleName: this.relatedTableName
                });
            }

            const optionIndex = _.findIndex(this.options, { id: optionId });

            this.options.splice(optionIndex, 1);

            this.emitChange();

            this.setFormVisibility(false);

            this.editedOptionId = null;
        },

        /**
         * Reveals the empty entry form so that a new option can be added
         *
         * @return  {void}
         */
        showForm() {
            this.formBootstrap();
        },

        /**
         * Toggles the form visibility
         *
         * @param   {boolean}  isVisible
         * @return  {void}
         */
        setFormVisibility (isVisible) {
            this.formVisible = isVisible;
        },

        /**
         * Fired upon hearing submitSuccess event
         *
         * @param   {string}  option.editorMode
         * @param   {integer}  option.id
         * @return  {void}
         */
        submitSuccess ({ editorMode, id, result }) {
            this.editedOptionId = null;

            this.setFormVisibility(false);

            if (editorMode === 'create') {
                this.options.push(result.entry);

                this.emitChange();
            }

            if (editorMode === 'edit') {
                this.options = this.options.map(option => {
                    if (option.id === id) {
                        return  result.entry;
                    }

                    return option;
                });
            }

        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.loadInitialValuesFromAPI();
    },

    /**
     * Define the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'admin.entryUpdated' (newEntry, oldEntry) {
            pWarn(
                'Watched property fired.',
                'Admin',
                'admin.entryUpdated',
                newEntry,
                oldEntry,
                this
            );

            this.fields = getEntryFields(this);

            this.resetFormFields();
        },
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `Extended.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                this.$nextTick(() => {
                    this.loadInitialValuesFromAPI();

                    this.resetFormFields();
                });
            }
        },
    },
};
