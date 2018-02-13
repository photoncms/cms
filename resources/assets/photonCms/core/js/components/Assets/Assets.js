import Vue from 'vue';

import { store } from '_/vuex/store';

import { mapGetters } from 'vuex';

import { eventBus } from '_/helpers/eventBus';

const photonConfig = require('~/config/config.json');

const FilePicker = Vue.component(
        'FilePicker',
        require('_/components/FieldTypes/AssetsManager/FilePicker/FilePicker.vue')
    );

const Modal = Vue.component(
        'Modal',
        require('_/components/UserInterface/Modal/Modal.vue')
    );

export default {
    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            /**
             * Toggles the entry editor visibility
             *
             * @type  {boolean}
             */
            entryEditorVisible: false,

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
             * Sets the asset manager in multiple selection mode
             *
             * @type  {boolean}
             */
            multiple: false,
        };
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
     * Set the components
     *
     * @return  {object}
     */
    components: {
        FilePicker,
        Modal,
    },

    /**
     * Set the computed properties
     *
     * @type  {object}
     */
    computed: {
        // Map getters
        ...mapGetters({
            admin: 'adminModal/adminModal',
            assetsManager: 'assets/assets',
            gallery: 'gallery/gallery',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        }),

        /**
         * Gets the assets manager state
         *
         * @return  {boolean}
         */
        assetsManagerVisible () {
            return this.assetsManager.assetsManagerVisible;
        },

        /**
         * Should Multiple File Uploader be used or not
         *
         * @return  {boolean}
         */
        useMultipleFileUploader () {
            return photonConfig.assetManager.useMultipleFileUploader;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Executes on close click (hides a modal)
         *
         * @return  {void}
         */
        closeAssetsManager() {
            store.dispatch('assets/assetsManagerVisible', { value: false });
        },

        /**
         * Executes on close click (hides a modal)
         *
         * @return  {void}
         */
        closeEntryEditor() {
            this.entryEditorVisible = false;
        },

        getAssetUrl ({ id, file_url }) {
            const imageNameSuffix = '_120x90';

            const fileExtensionWithDot = /(?:\.([^.]+))?$/.exec(file_url)[0];

            const fileNameWithoutExtension = file_url.slice(0, fileExtensionWithDot.length * -1);

            return `${fileNameWithoutExtension}${imageNameSuffix}${fileExtensionWithDot}?${id}`;
        },

        /**
         * Initializes the asset manager
         *
         * @return  {void}
         */
        initializeAssetManager() {
            // Initialize the asset manager state
            store.dispatch(
                'assets/initializeState', {
                    multiple: this.multiple,
                    value: {},
                });

            // Populate the selectedFiles data property
            store.dispatch(
                'assets/getSelectedAssets', {
                    selectedAssetsIds: this.assetsManager.selectedAssetsIds,
                });
        },

        /**
         * Bind evenBus listeners
         *
         * @return  {void}
         */
        initEventBusListener () {
            eventBus.$off('dropZoneUploadComplete');

            eventBus.$on('dropZoneUploadComplete', (response) => {
                this.newFileUploaded(response);
            });
        },

        /**
         * Executed when a new file was uploaded
         *
         * @param   {object}  response
         * @return  {void}
         */
        newFileUploaded( response) {
            if (response.body.entry) {
                store.dispatch('assets/updateAssetsEntries', { values: response.body.entry });
            }
        },

        /**
         * Shows modal window
         *
         * @return  {void}
         */
        openAssetsManager() {
            store.dispatch(
                'assets/initializeState', {
                    multiple: this.multiple,
                    value: [],
                });

            store.dispatch('assets/assetsManagerVisible', { value: true });
        },

        /**
         * Prepares the UI for new upload
         *
         * @return  {void}
         */
        prepareForNewUploadAction () {
            store.dispatch('assets/prepareForNewUpload');

            const $element = $(this.$el).find('#accordion-asset-details-label');

            if($($element).hasClass('collapsed')) {
                $(this.$el).find('#accordion-asset-details-label').click();
            }
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
         * Runs the selectAsset Action
         *
         * @param   {object}  asset
         * @return  {void}
         */
        selectAssetAction ({ asset }) {
            store.dispatch('assets/selectAsset', { asset });
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.initializeAssetManager();

            this.initEventBusListener();
        });
    },
};
