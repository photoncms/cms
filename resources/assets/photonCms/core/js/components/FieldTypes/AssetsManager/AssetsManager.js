import Vue from 'vue';

import _ from 'lodash';

import { store } from '_/vuex/store';

const photonConfig = require('~/config/config.json');

import { eventBus } from '_/helpers/eventBus';

import { formatBytes } from '_/helpers/formatBytes';

import {
    mapGetters,
    mapActions,
} from 'vuex';

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
     * Define props
     *
     * @type  {Object}
     */
    props: {
        disabled: {
            type: Boolean,
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
        refreshFields: {
            type: Number,
        },
        value: {
            default: null,
            type: [
                Array,
                Number,
                Object,
            ],
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
             * Toggles the visibility of clear all selected assets confirmation buttons
             *
             * @type  {boolean}
             */
            confirmClearAllSelectedAssets: false,

            /**
             * Selected assets, copied from asset manager modal after 'save & close' action
             *
             * @type  {Array}
             */
            selectedAssets: [],

            /**
             * Selected asset IDs, copied from asset manager modal after 'save & close' action
             *
             * @type  {Array}
             */
            selectedAssetsIds: [],
        };
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
            admin: 'admin/admin',
        }),

        /**
         * Define the assetsManager getter
         *
         * @return  {object}
         */
        assetsManager () {
            const getterName = `${this.registeredModuleName}/${this.registeredModuleName}`;

            return this.$store.getters[getterName];
        },

        /**
         * Gets the assets manager state
         *
         * @return  {boolean}
         */
        assetsManagerVisible () {
            return this.assetsManager.assetsManagerVisible;
        },

        /**
         * Is the clear files button disabled
         *
         * @return  {boolean}
         */
        clearFilesButtonDisabled () {
            return (this.disabled || _.isEmpty(this.selectedAssets));
        },

        /**
         * Gets the selected assets
         *
         * @return  {array}
         */
        getValue: function() {
            if(!this.multiple) {
                return this.selectedAssetsIds.length > 0
                    ? this.selectedAssetsIds[0]
                    : '';
            }

            return this.selectedAssetsIds;
        },

        hasSelectedAssets () {
            if (_.isEmpty(this.assetsManager.selectedAssets)) {
                return false;
            }

            return true;
        },

        /**
         * Gets the registeredModuleName
         *
         * @return  {string}
         */
        registeredModuleName () {
            return `assetsManager-${this.name}-${this.id}`;
        },

        /**
         * Select after upload getter/setter
         *
         * @type  {void}
         */
        selectAfterUpload: {
            get () {
                return this.assetsManager.selectAfterUpload;
            },
            set (value) {
                this.selectAfterUploadAction(value);
            }
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
     * @type  {object}
     */
    methods: {
        // Map actions from admin module namespace
        ...mapActions('admin', [
            'deleteAsset',
            'updateAssetsEntries',
        ]),

        /**
         * Resets the file input value
         *
         * @return  {void}
         */
        clearAllSelectedAssets () {
            this.setConfirmClearAllSelectedAssets(false);

            store.dispatch(`${this.registeredModuleName}/clearAllSelectedAssets`);

            this.selectedAssets = [];

            this.selectedAssetsIds = [];

            this.$emit('change', {
                id: this.id,
                name: this.name,
                value: this.getValue,
            });
        },

        /**
         * Formats byte to human readable value
         *
         * @param   {integer}  bytes
         * @return  {string}
         */
        formatBytes,

        /**
         * Prepares the UI for new upload
         *
         * @return  {void}
         */
        prepareForNewUploadAction () {
            store.dispatch(`${this.registeredModuleName}/prepareForNewUpload`);

            const $element = $(this.$el).find('#accordion-asset-details-label');

            if($($element).hasClass('collapsed')) {
                $(this.$el).find('#accordion-asset-details-label').click();
            }
        },

        /**
         * Executes on close click (hides a modal)
         *
         * @return  {void}
         */
        closeAssetsManager () {
            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: false });
        },

        /**
         * Initializes the asset manager
         *
         * @return  {void}
         */
        initializeAssetManager () {
            store.dispatch(
                `${this.registeredModuleName}/initializeState`, {
                    multiple: this.multiple,
                    value: this.value,
                }).then(() => {
                    // Populate the selectedFiles data property
                    store.dispatch(
                        `${this.registeredModuleName}/getSelectedAssets`, {
                            selectedAssetsIds: this.assetsManager.selectedAssetsIds,
                        })
                        .then((response) => {
                            this.selectedAssets = response;
                        });
                });
        },

        /**
         * Bind evenBus listeners
         *
         * @return  {void}
         */
        initEventBusListener () {
            // eventBus.$off('dropZoneUploadComplete');

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
        newFileUploaded (response) {
            if (response.body.entry) {
                store.dispatch(`${this.registeredModuleName}/updateAssetsEntries`, { values: response.body.entry });
            }

            if(this.assetsManager.selectAfterUpload) {
                store.dispatch(
                    `${this.registeredModuleName}/selectAsset`,
                    {
                        asset: response.body.entry,
                        selectActiveAsset: false,
                    });
            }
        },

        /**
         * Shows modal window
         *
         * @return  {void}
         */
        openAssetsManager () {
            this.initializeAssetManager();

            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: true });
        },

        /**
         * Dispatches the selectAfterUpload action
         *
         * @param   {boolean}  value
         * @return  {void}
         */
        selectAfterUploadAction (value) {
            return store.dispatch(`${this.registeredModuleName}/selectAfterUpload`, { value });
        },

        /**
         * Executes on select & close click (hides a modal, and marks the selection as complete)
         *
         * @return  {void}
         */
        selectAssets (close = true) {
            if (close) {
                store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: false });
            }

            this.selectedAssets = this.assetsManager.selectedAssets;

            this.selectedAssetsIds = this.assetsManager.selectedAssetsIds;

            this.$emit('change', {
                id: this.id,
                value: this.getValue,
            });
        },

        /**
         * Runs the selectAsset Action
         *
         * @param   {object}  asset
         * @return  {void}
         */
        selectAssetAction ({ asset }) {
            store.dispatch(`${this.registeredModuleName}/selectAsset`, { asset });
        },

        /**
         * Toggles the visibility of ClearAllSelectedAssets confirmation buttons
         *
         * @param  {boolean}  value
         * @return  {void}
         */
        setConfirmClearAllSelectedAssets (value) {
            this.confirmClearAllSelectedAssets = value;
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            $(this.$el).find('.close-footer input[type="checkbox"]').uniform();

            this.initEventBusListener();

            this.initializeAssetManager();
        });
    },

    watch: {
        'refreshFields'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.initializeAssetManager();
            }
        },
    },
};
