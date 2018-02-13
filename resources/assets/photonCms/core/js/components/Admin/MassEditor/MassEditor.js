import _ from 'lodash';

import { getEntryFields } from '_/components/Admin/Admin.fields';

const photonConfig = require('~/config/config.json');

import {
    mapGetters,
    mapActions,
} from 'vuex';

import { pWarn } from '_/helpers/logger';

import {
    bindValidatorToForm,
    destroyValidator,
    processErrors,
    resetValidator,
} from '_/services/formValidator';

export default {
    data: function() {
        return {
            fields: [],
            formFieldsReset: {
                includeFields: [],
                resetData: null,
            },
            serverError: null,
            vuexModule: 'admin',
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
            advancedSearch: 'advancedSearch/advancedSearch',
            photonModule: 'photonModule/photonModule',
        }),
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('admin', [
            'massEdit',
        ]),

        ...mapActions('advancedSearch', [
            'hideMassEditor',
            'showMassEditor',
        ]),

        /**
         * Clears the errors from the UI
         *
         * @return  {void}
         */
        clearErrors () {
            this.serverError = null;
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
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.validator = bindValidatorToForm({
            selector: '#mass-editor-form',

            onSubmit: _.debounce(() => {
                this.setSubmitEntryInProgress(true);

                this.clearAdminErrors();

                resetValidator(this.validator);

                this.massEdit()
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

        // Gets entry field options defined in Entry.fields
        this.fields = getEntryFields(this);
    },

    beforeDestroy: function() {
        if (this.validator) {
            destroyValidator(this.validator);
        }
    },

    watch: {
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
