import _ from 'lodash';

import Vue from 'vue';

import { store } from '_/vuex/store';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

import { imageTagTemplate } from '~/components/FieldTypes/Redactor/Redactor.imageTagTemplate';

import '~/components/FieldTypes/Redactor/Redactor.alignment.plugin';

import '~/components/FieldTypes/Redactor/Redactor.clips.plugin';

import '~/components/FieldTypes/Redactor/Redactor.editPhotonImage.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fontcolor.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fontsize.plugin';

import '~/components/FieldTypes/Redactor/Redactor.source.plugin';

import '~/components/FieldTypes/Redactor/Redactor.video.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fullscreen.plugin';

import '~/components/FieldTypes/Redactor/Redactor.embedCode.plugin';

import { eventBus } from '_/helpers/eventBus';

const photonConfig = require('~/config/config.json');

import { mapGetters } from 'vuex';

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
        refreshFields: {
            type: Number,
        },
        value: {
            type: String,
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            $redactorContainer: null,
        };
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

    methods: {
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
        initializeAssetManager() {
            // Initialize the asset manager state
            store.dispatch(
                `${this.registeredModuleName}/initializeState`, {
                    multiple: this.multiple,
                    value: this.value,
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
         * Initializes the Redactor plug-in
         *
         * @return  {void}
         */
        initializeRedactor: function() {
            const self = this;

            this.$redactorContainer = $(self.$el).find('textarea');

            this.$redactorContainer.redactor({
                ...redactorConfig,
                callbacks: {
                    change: function () {
                        self.onChange(self.id, self.name, this.code.get());
                    },
                    init: function () {
                        let imageButton = this.button.add('image', 'Upload Image');

                        this.button.setIcon(imageButton, '<i class="fa fa-picture-o"></i>');

                        this.button.addCallback(imageButton, function () {
                            self.$redactorContainer.redactor('selection.save');

                            self.openAssetsManager();
                        });
                    }
                },
                // pass a reference to Vue so that we can use the instance in plugins
                Vue: this,
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

        onChange: function(id, name, value) {
            this.$emit('change', {
                id,
                name,
                value,
            });
        },

        onAssetSelection () {
            // const this.$redactorContainer = $(this.$el).find(`#${this.id}`);

            const isModalVisible = $('#redactor-modal-box').is(':visible');

            if (!_.isEmpty(this.assetsManager.selectedAssets)) {
                this.$redactorContainer.redactor('selection.restore');

                let asset = Object.assign({}, this.assetsManager.selectedAssets[0]);

                this.initializeAssetManager();

                /**
                 * If Redactor modal is visible, update the modal content only
                 */
                if(isModalVisible) {
                    let $modal = this.$redactorContainer.redactor('modal.getModal');

                    $($modal).find('#photon-asset-id').val(asset.id);

                    const imageSizeId = $($modal).find('#photon-image-size').val();

                    const previewUrl = _.find(asset.resized_images, { image_size: parseInt(imageSizeId) });

                    $($modal).find('#photon-image-preview img').attr('src', previewUrl.file_url);

                    return;
                }

                const tempId = `image-tag-${moment().valueOf()}`;

                const template = imageTagTemplate(asset, tempId);

                this.$redactorContainer.redactor('insert.raw', template);

                this.$redactorContainer.redactor('code.sync');

                $(`#${tempId}`).click()
                    .removeAttr('id');
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
                    value: this.value,
                });

            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: true });
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
         * Executes on select & close click (hides a modal, and marks the selection as complete)
         *
         * @return  {void}
         */
        selectAssets () {
            store.dispatch(`${this.registeredModuleName}/assetsManagerVisible`, { value: false });

            this.onAssetSelection();
        },

        /**
         * Runs the selectAsset Action
         *
         * @param   {object}  asset
         * @return  {void}
         */
        selectAssetAction ({ asset }) {
            store.dispatch(`${this.registeredModuleName}/selectAsset`, { asset });
        }
    },

    mounted: function() {
        this.$nextTick(() => {
            $(this.$el).find('.close-footer input[type="checkbox"]').uniform();

            this.initializeRedactor();

            this.initEventBusListener();

            this.initializeAssetManager();
        });
    },

    beforeDestroy: function() {
        $(this.$el).find(`#${this.id}`).redactor('core.destroy');
    },

    watch: {
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.$forceUpdate();

                // const this.$redactorContainer = $(this.$el).find(`#${this.id}`);

                this.$redactorContainer.redactor('core.destroy');

                this.$redactorContainer.val(this.value);

                this.initializeRedactor();

                this.initializeAssetManager();
            }
        },
    },
};
