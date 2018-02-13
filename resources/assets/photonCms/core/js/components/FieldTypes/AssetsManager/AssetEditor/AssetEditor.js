import _ from 'lodash';

import { getEntryFields } from '_/components/Admin/Admin.fields';

import { mapGetters } from 'vuex';

import { formatBytes } from '_/helpers/formatBytes';

import { store } from '_/vuex/store';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        assetsManager: {
            type: Object,
            required: true,
        },
        updateAssetEditor: {
            type: [
                Number,
                String,
            ],
        }
    },

    /**
     * Setup the beforeCreate hook
     *
     * @return  {void}
     */
    beforeCreate () {
        /**
         * Then EntryForm component is loaded recursively, and the proposed method that suggests that it's OK to have
         * recursive components as long as you have a unique name for each failed.
         * Evan suggested this undocumented solution in https://github.com/vuejs/vue/issues/4117 to fix the issue.
         */
        this.$options.components.EntryForm = require('_/components/UserInterface/EntryForm/EntryForm.vue');
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
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
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        }),

        /**
         * Map the assetManager model as admin
         *
         * @return  {object}
         */
        admin () {
            return this.assetsManager;
        },

        /**
         * Created at getter
         *
         * @return  {string}
         */
        createdAt () {
            return this.admin.editedEntry.created_at
                ? moment(
                    this.admin.editedEntry.created_at
                ).format(this.dateFormat)
                : null;
        },

        /**
         * Date format getter
         *
         * @return  {string}
         */
        dateFormat () {
            return this.ui.photonConfig.dateFormat;
        },

        /**
         * Checks if this.fields is populated
         *
         * @return  {Boolean}
         */
        hasFields () {
            return !_.isEmpty(this.fields);
        },

        /**
         * Returns true if there's an asset selected
         *
         * @return  {Boolean}
         */
        isAssetSelected () {
            return _.has(this.assetsManager.editedEntry, 'id');
        },

        /**
         * Updated at getter
         *
         * @return  {string}
         */
        updatedAt () {
            return this.admin.editedEntry.updated_at
                ? moment(
                    this.admin.editedEntry.updated_at
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
         * Formats byte to human readable value
         *
         * @param   {integer}  bytes
         * @return  {string}
         */
        formatBytes,

        /**
         * Gets Entry Fields, along with some field data transformations
         *
         * @return  {void}
         */
        getFields () {
            this.fields = getEntryFields(this);

            this.fields = this.fields.map(field => {
                if (field.name === 'file' && this.admin.editorMode !== 'create') {
                    field['hidden'] = true;
                }

                field.mutation = `${this.assetsManager.moduleName}/UPDATE_ASSET_MODULE_ENTRY_FIELD`;

                return field;
            });

            this.resetFormFields();
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
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            store.dispatch('photonModule/getPhotonModuleInformation', { moduleTableName: 'assets' })
                .then(() => {
                    const selectedModule = this.photonModule.moduleInformation.assets;

                    store.dispatch(`${this.assetsManager.moduleName}/updateSelectedModule`, { module: selectedModule });

                    this.fields = getEntryFields(this);

                    this.fields = this.fields.map(field => {
                        field.mutation = `${this.assetsManager.moduleName}/UPDATE_ASSET_MODULE_ENTRY_FIELD`;

                        return field;
                    });
                });
        });
    },

    /**
     * Define the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'admin.entryUpdated' () {
            this.getFields();
        },

        'admin.editorMode' (newEntry, oldEntry) {
            if (newEntry == 'create') {
                this.getFields();
            }
        },

        'updateAssetEditor' () {
            this.getFields();
        },
    },
};
