import _ from 'lodash';

import Vue from 'vue';

import {
    mapGetters,
    mapActions,
} from 'vuex';

import { pWarn } from '_/helpers/logger';

import { router } from '_/router/router';

import { store } from '_/vuex/store';

import { userHasRole } from '_/vuex/actions/userActions';

import * as adminSlots from '~/config/adminSlots';

import { storage } from '_/services/storage';

import { eventBus } from '_/helpers/eventBus';

// import { downloadFile } from '_/vuex/actions/advancedSearchActions';

import {
    bindValidatorToForm,
    destroyValidator,
    processErrors,
    resetValidator,
} from '_/services/formValidator';

const photonConfig = require('~/config/config.json');

const FormField = Vue.component('FormField', require('_/components/UserInterface/EntryForm/FormField/FormField.vue'));

const ImpersonateButton = Vue.component(
        'ImpersonateButton',
        require('_/components/UserInterface/EntryForm/ImpersonateButton/ImpersonateButton.vue')
    );

const InvitationsButtons = Vue.component(
        'InvitationsButtons',
        require('_/components/UserInterface/EntryForm/InvitationsButtons/InvitationsButtons.vue')
    );

export const PermissionNameGeneratorHandler = Vue.component(
        'PermissionNameGeneratorHandler',
        require('_/components/UserInterface/PermissionNameGeneratorHandler/PermissionNameGeneratorHandler.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        disableCreateAnother: {
            default: false,
            type: Boolean,
        },
        disableDelete: {
            default: false,
            type: Boolean,
        },
        extendedFieldEditing: {
            default: false,
            type: Boolean,
        },
        fields: {
            type: Array,
        },
        formFieldsResetProp: {
            required: true,
            type: Object,
        },
        inSidebar: {
            default: false,
            type: Boolean,
        },
        shouldRouterPush: {
            default: true,
            type: Boolean,
        },
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            formFieldsReset: {
                includeFields: [],
                resetData: null,
            },
            serverError: null,
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        ...adminSlots,
        FormField,
        ImpersonateButton,
        InvitationsButtons,
        PermissionNameGeneratorHandler,
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('admin', [
            'setCreateEntryUI',
        ]),

        ...mapActions('ui', [
            'triggerCreateMode',
        ]),

        ...mapActions('user', [
            'checkMe',
        ]),

        /**
         * Dispatches the clearAdminErrors action
         *
         * @return  {void}
         */
        clearAdminErrors () {
            return store.dispatch(`${this.vuexModule}/clearAdminErrors`);
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
         * Dispatches the confirmDeleteEntry action
         *
         * @return  {void}
         */
        confirmDeleteEntry () {
            return store.dispatch(`${this.vuexModule}/confirmDeleteEntry`);
        },

        /**
         * Dispatches the createAnother action
         *
         * @param   {boolean}  value
         * @return  {void}
         */
        createAnotherEntry (value) {
            return store.dispatch(`${this.vuexModule}/createAnother`, { value });
        },

        /**
         * Commits the deleteEntry action
         *
         * @param   {integer}  options.entryId
         * @param   {string}  options.selectedModuleName
         * @return  {promise}
         */
        deleteEntry ({ entryId, selectedModuleName }) {
            store.dispatch(`${this.vuexModule}/deleteEntry`, { entryId, selectedModuleName })
                .then(() => {
                    if (this.shouldRouterPush) {
                        router.push(`/admin/${selectedModuleName}`);
                    }

                    store.dispatch('admin/setAdminUi');
                });
        },

        /**
         * Prepares the payload object and runs the deleteEntry method
         *
         * @return  {promise}
         */
        deleteEntryConfirmed () {
            return this.deleteEntry({
                entryId: this.admin.editedEntry.id,
                selectedModuleName: this.admin.selectedModule.table_name,
            });
        },

        /**
         * Bind evenBus listeners
         *
         * @return  {void}
         */
        initEventBusListener () {
            eventBus.$on('dropZoneQueueComplete', (result) => {
                if (this.admin.selectedModule.table_name == 'assets') {
                    this.onSubmitSuccess(result);
                }
            });
        },

        /**
         * Returns the fields that should be rendered inline
         *
         * @return  {array}
         */
        inlineFieldTypes (field) {
            // If the entry form is rendered in sidebar none of the fields should be inline
            if (this.inSidebar) {
                return false;
            }

            if (_.has(field, 'inline')) {
                return field.inline;
            }

            const inlineFieldTypes = photonConfig.inlineFieldTypes;

            return (inlineFieldTypes.indexOf(field.vueComponent) === -1) ? true : false;
        },

        /**
         * Runs on successful submit
         *
         * @param   {object}  results
         * @return  {void}
         */
        onSubmitSuccess (result) {
            if(!this.extendedFieldEditing && this.shouldRouterPush) {
                if((this.admin.editorMode === 'create' || this.admin.editorMode === 'default')
                    && !this.admin.createAnother
                    && this.admin.selectedModule.table_name != 'assets') {
                    router.push(`/admin/${this.admin.selectedModule.table_name}/${this.admin.entry.id}`);
                }
            }

            this.$emit('submitSuccess', {
                editorMode: this.admin.editorMode,
                id: this.admin.entry.id,
                result,
            });

            this.setSubmitEntryInProgress(false);

            if (!result) {
                return;
            }

            // Refresh user meta data if editing own profile under users module
            if (this.admin.selectedModule.table_name === 'users'
                && this.user.meta.id === result.entry.id) {
                const apiToken = storage.get('apiToken');

                this.checkMe({ apiToken });
            }

            resetValidator(this.validator);

            this.clearErrors();

            this.resetFormFields();
        },

        /**
         * Dispatches the submitEntry action
         *
         * @return  {promise}
         */
        submitEntry () {
            if (this.admin.selectedModule.table_name === 'assets'
                && this.admin.editorMode === 'create'
                && this.vuexModule !== 'menuItemsEditor') {
                // handle the submission process to Dropzone
                eventBus.$emit('submitSelectedAssets', {});

                return Promise.resolve(false);
            }

            return store.dispatch(`${this.vuexModule}/submitEntry`);
        },

        /**
         * Dispatches the setSubmitEntryInProgress action
         *
         * @param   {boolean}  value
         * @return  {promise}
         */
        setSubmitEntryInProgress (value) {
            return store.dispatch(`${this.vuexModule}/setSubmitEntryInProgress`, { value });
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
         * Checks if a user has given role
         *
         * @param   {string}  role
         * @return  {bool}
         */
        userHasRole,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            user: 'user/user',
        }),

        /**
         * Create the admin getter based on the vuexModule prop
         *
         * @return  {object}
         */
        admin () {
            const getterName = `${this.vuexModule}/${this.vuexModule}`;

            return this.$store.getters[getterName];
        },

        createAnother: {
            get () {
                return this.admin.createAnother;
            },
            set (value) {
                this.createAnotherEntry(value);
            }
        },

        /**
         * Sets the action button text
         *
         * @return  {string}
         */
        newEntryButtonText () {
            if (this.vuexModule === 'admin' && this[this.vuexModule].selectedModule.table_name === 'invitations') {
                return this.$t('admin.sendInvitation');
            }

            return this.$t('admin.create');
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.initEventBusListener();

            // Use uniform to style checkbox/radio elements
            $(this.$el).find('.form-footer input[type="checkbox"]').uniform();

            // Bootstrap validator for the form
            this.validator = bindValidatorToForm({
                selector: `#entry-editor-form-${this.vuexModule}`,

                onSubmit: _.debounce(() => {
                    this.setSubmitEntryInProgress(true);

                    // On submit clear any errors and submit entry changes
                    this.clearAdminErrors();

                    resetValidator(this.validator);

                    this.submitEntry()
                        .then((result) => {
                            // Don't run onSubmitSuccess() for assets module, as it will be ran async after being
                            // triggered by a global event via eventBus
                            if(this.admin.selectedModule.table_name == 'assets') {
                                this.setSubmitEntryInProgress(false);

                                return;
                            }

                            this.onSubmitSuccess(result);
                        });
                }, 250),
            });
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        if (this.validator) {
            destroyValidator(this.validator);
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
                pWarn(
                    'Watched property fired.',
                    'EntryForm',
                    'formFieldsResetProp',
                    newEntry,
                    oldEntry,
                    this
                );

                $(this.$el).find('.form-footer input[type="checkbox"]').uniform();

                this.resetFormFields(newEntry);

                resetValidator(this.validator);

                this.clearErrors();
            },
        },

        /**
         * On entryFormError change
         *
         * @return  {void}
         */
        'admin.error': {
            deep: true,
            handler (newEntry, oldEntry) {
                pWarn(
                    'Watched property fired.',
                    'EntryForm',
                    'formFieldsResetProp',
                    newEntry,
                    oldEntry,
                    this
                );

                if (this.validator) {
                    resetValidator(this.validator);
                }

                this.clearErrors();

                this.serverError = processErrors(this.validator, this.admin.error);
            },
        }
    }
};
