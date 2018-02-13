import _ from 'lodash';

import Vue from 'vue';

import { api } from '_/services/api';

import { store } from '_/vuex/store';

import { mapGetters } from 'vuex';

import { eventBus } from '_/helpers/eventBus';

import {
    pLog,
    pWarn,
    pError
} from '_/helpers/logger';

const photonConfig = require('~/config/config.json');

const DropZone = Vue.component(
        'DropZone',
        require('_/components/UserInterface/DropZone/DropZone.vue')
    );

const FilePicker = Vue.component(
        'FilePicker',
        require('_/components/FieldTypes/AssetsManager/FilePicker/FilePicker.vue')
    );

const Modal = Vue.component(
        'Modal',
        require('_/components/UserInterface/Modal/Modal.vue')
    );

import { getEntryFields } from '_/components/Admin/Admin.fields';

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
             * Toggles the assets manager delete confirmation buttons
             *
             * @type  {boolean}
             */
            galleryDeleteConfirmationVisible: false,

            /**
             * The gallery item id
             *
             * @type  {integer}
             */
            galleryId: null,

            /**
             * Sets the asset manager in multiple selection mode
             *
             * @type  {boolean}
             */
            multiple: true,

            /**
             * Stores the gallery items
             *
             * @type  {Array}
             */
            galleryItems: [],
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
        DropZone,
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
            gallery: 'gallery/gallery',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
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
         * Gets the selected asset
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
     * @type  {Object}
     */
    methods: {
        /**
         * Executes on close click (hides a modal)
         *
         * @return  {void}
         */
        closeAssetsManager() {
            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: false });
        },

        /**
         * Executes on close click (hides a modal)
         *
         * @return  {void}
         */
        closeEntryEditor() {
            this.entryEditorVisible = false;
        },

        /**
         * Creates a new gallery entry and binds the returned gallery id to current module entry
         *
         * @return  {void}
         */
        createGallery() {
            const timestamp = moment().valueOf();

            store.dispatch('gallery/createGallery', { title: `${this.name}${timestamp}` })
                .then(response => {
                    this.galleryId = response.data.body.entry.id;

                    this.$emit('change', {
                        event,
                        id: this.id,
                        name: this.name,
                        value: this.galleryId,
                    });

                    this.openAssetsManager();
                }).catch(() => {
                    pError('Could not create new gallery entry.', `${this.name}${timestamp}`);
                });
        },

        /**
         * Removes a gallery
         *
         * @return  {[type]}  [description]
         */
        deleteGallery () {
            this.galleryId = null;

            this.galleryItems = [];

            this.$emit('change', {
                event,
                id: this.id,
                name: this.name,
                value: null,
            });

            this.predeleteGallery();
        },

        /**
         * Deletes a gallery item
         *
         * @param   {integer}  assetId
         * @return  {void}
         */
        deleteGalleryItem (assetId) {
            store.dispatch('gallery/deleteGalleryItem', { assetId })
                .then(() => {
                    const index = _.findIndex(this.galleryItems, { 'id': assetId });

                    this.galleryItems.splice(index, 1);
                });
        },

        getAssetUrl ({ id, file_url }) {
            const imageNameSuffix = '_120x90';

            const fileExtensionWithDot = /(?:\.([^.]+))?$/.exec(file_url)[0];

            const fileNameWithoutExtension = file_url.slice(0, fileExtensionWithDot.length * -1);

            return `${fileNameWithoutExtension}${imageNameSuffix}${fileExtensionWithDot}?${id}`;
        },

        /**
         * Initializes the gallery field
         *
         * @return  {void}
         */
        initializeGalleryField() {
            this.galleryId = null;

            this.galleryItems = [];

            if(!_.isEmpty(this.value) && _.isObject(this.value)) {
                this.galleryId = _.has(this.value, 'id') ? this.value.id : null;
            }

            if (!this.galleryId) {
                return;
            }

            const payload = {
                filter: {
                    scope_id: {
                        equal: {
                            id: this.galleryId,
                        }
                    },
                },
            };

            return api.post('filter/gallery_items', payload)
                .then((response) => {
                    this.galleryItems = response.data.body.entries;
                })
                .catch((response) => {
                    pError('Failed to load gallery items.', payload, response);
                });
        },

        /**
         * Initializes the asset manager
         *
         * @return  {void}
         */
        initializeAssetManager() {
            // Initialize the asset manager state
            store.dispatch(
                `${this.registeredModuleName}/initializeState`, {
                    multiple: this.multiple,
                    value: {},
                });

            // Populate the selectedFiles data property
            store.dispatch(
                `${this.registeredModuleName}/getSelectedAssets`, {
                    selectedAssetsIds: this.assetsManager.selectedAssetsIds,
                });
        },

        /**
         * Initializes the UI Sortable
         *
         * @return  {void}
         */
        initializeUISortable () {
            const $uiSortable = $(this.$el).find('#ui-sortable');

            if ($uiSortable.hasClass('ui-sortable')) {
                $uiSortable.sortable('destroy');
            }

            $uiSortable.sortable({
                containment: 'parent',
                opacity: 0.6,
                placeholder: 'placeholder',
                tolerance: 'pointer',
                start: function(e, ui) {
                    $(this).attr('data-previndex', ui.item.index());
                },
                stop: function(event, ui) {
                    const oldIndex = $(this).attr('data-previndex');

                    const newIndex = ui.item.index();

                    if (parseInt(oldIndex) !== parseInt(newIndex)) {
                        const nextItemId = ui.item.next().data('galleryItemId');

                        if (nextItemId) {
                            store.dispatch('gallery/repositionGalleryItem', {
                                action: 'moveToLeftOf',
                                affectedItemId: ui.item.data('galleryItemId'),
                                targetItemId: nextItemId
                            })
                            .then(() => {
                                pLog('Succesfully moved the gallery item.');
                            })
                            .catch(() => {
                                pError('Failed moving, canceling the UI move.');
                                $uiSortable.sortable('cancel');
                            });

                            return;
                        }

                        const previousItemId = ui.item.prev().data('galleryItemId');

                        if (previousItemId) {
                            store.dispatch('gallery/repositionGalleryItem', {
                                action: 'moveToRightOf',
                                affectedItemId: ui.item.data('galleryItemId'),
                                targetItemId: previousItemId
                            })
                            .then(() => {
                                pLog('Succesfully moved the gallery item.');
                            })
                            .catch(() => {
                                pError('Failed moving, canceling the UI move.');
                                $uiSortable.sortable('cancel');
                            });
                        }
                    }

                    $(this).removeAttr('data-previndex');
                }
            });

            $uiSortable.disableSelection();
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
        newFileUploaded(response) {
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
        openAssetsManager() {
            // Initialize the asset manager state
            store.dispatch(
                `${this.registeredModuleName}/initializeState`, {
                    multiple: this.multiple,
                    value: [],
                });

            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: true });
        },

        /**
         * Opens the entry editor
         *
         * @param   {integer}  entryId
         * @return  {void}
         */
        openEntryEditor (entryId) {
            store.dispatch('adminModal/adminBootstrap', {
                moduleTableName: 'gallery_items',
                moduleEntryId: entryId,
                shouldFetchSearch: false,
            })
                .then(() => {

                    this.entryEditorVisible = true;

                    this.fields = getEntryFields(this);

                    this.resetFormFields();
                })
                .catch((error) => {
                    pError('adminModal/adminBootstrap action failed with an error', error);
                });
        },

        /**
         * Toggles deletion confirmation dialogue
         *
         * @return  {void}
         */
        predeleteGallery () {
            this.galleryDeleteConfirmationVisible = !this.galleryDeleteConfirmationVisible;
        },

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
            store.dispatch(`${this.registeredModuleName}/selectAsset`, { asset });
        },

        /**
         * Executes on select (and close) action
         *
         * @return  {void}
         */
        selectAssets (close = true) {
            if (close) {
                store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: false });
            }

            const selectedAssets = this.assetsManager.selectedAssets;


            if(!_.isEmpty(selectedAssets)) {
                let promiseContainer = Promise.resolve();

                // const promises = [];

                selectedAssets.forEach(asset => {
                    promiseContainer = promiseContainer
                        .then(() => {
                            return store.dispatch('gallery/createGalleryItem', {
                                assetId: asset.id,
                                scopeId: this.galleryId,
                            })
                                .then(response => {
                                    this.galleryItems.push(response.data.body.entry);
                                });
                        });

                    // promises.push(promise);
                });

                // Promise.all(promises)
                promiseContainer
                    .then((responsesArray) => {
                        // if (!_.isEmpty(responsesArray)) {
                        //     responsesArray.forEach(response => {
                        //         this.galleryItems.push(response.data.body.entry);
                        //     });
                        // }

                        this.initializeUISortable();

                        store.dispatch(`${this.registeredModuleName}/clearAllSelectedAssets`);
                    })
                    .catch((error) => {
                        pError('Failed creating gallery entries.', error);
                    });
            }
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
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            $(this.$el).find('.close-footer input[type="checkbox"]').uniform();

            this.initializeGalleryField();

            this.initEventBusListener();

            this.initializeAssetManager();
        });
    },

    /**
     * Define the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'refreshFields'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `Gallery.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                this.initializeGalleryField();

                // this.initializeAssetManager();
            }
        },

        'admin.entryUpdated' (newEntry, oldEntry) {
            pWarn(
                'Watched property fired.',
                'Admin',
                'admin.entryUpdated',
                newEntry,
                oldEntry,
                this
            );

            this.closeEntryEditor();

            // this.initializeGalleryField();

            // this.initializeAssetManager();
        },
    },
};
