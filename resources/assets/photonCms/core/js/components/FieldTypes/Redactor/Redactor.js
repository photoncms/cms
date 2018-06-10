import _ from 'lodash';

import Vue from 'vue';

import { store } from '_/vuex/store';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

import { imageTagTemplate } from '~/components/FieldTypes/Redactor/Redactor.imageTagTemplate';

import '~/components/FieldTypes/Redactor/Redactor.alignment.plugin';

import '~/components/FieldTypes/Redactor/Redactor.clips.plugin';

import '~/components/FieldTypes/Redactor/Redactor.counter.plugin';

import '~/components/FieldTypes/Redactor/Redactor.editPhotonImage.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fontcolor.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fontsize.plugin';

import '~/components/FieldTypes/Redactor/Redactor.video.plugin';

import '~/components/FieldTypes/Redactor/Redactor.fullscreen.plugin';

import '~/components/FieldTypes/Redactor/Redactor.widget.plugin';

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

            // make sure that the existing instance is destroyed before initializing a new one
            if(this.$redactorContainer.redactor('isStarted')) {
                this.$redactorContainer.redactor('destroy');
            }

            this.$redactorContainer.redactor({
                ...redactorConfig,
                openAssetsManager: self.openAssetsManager,
                callbacks: {
                    changed: function (html) {
                        self.onChange(self.id, self.name, html);
                    },
                    source: {
                        changed: function (html) {
                            self.onChange(self.id, self.name, html);
                        },
                    },
                },
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

        /**
         * Triggered on asset selection
         *
         * @return  {void}
         */
        onAssetSelection () {
            const isModalVisible = $('#redactor-modal').is(':visible');

            if (!_.isEmpty(this.assetsManager.selectedAssets)) {
                this.$redactorContainer.redactor('selection.restore');

                let asset = Object.assign({}, this.assetsManager.selectedAssets[0]);

                this.initializeAssetManager();

                /**
                 * If Redactor modal is visible, update the modal content only
                 */
                if(isModalVisible) {
                    $('#redactor-modal form').trigger('assetChanged', [ asset ]);

                    return;
                }

                const $template = imageTagTemplate(asset);

                const $node = $R.dom('<figure>')
                    .addClass('redactor-component')
                    .addClass('photon-image-container')
                    .data('redactor-type', 'widget')
                    .data('widget-type', 'photon-image')
                    .data('open-first', true)
                    .html($template);

                    $node.attr('data-widget-code', encodeURI($node.nodes[0].innerHTML.trim()));

                this.$redactorContainer.redactor('insertion.insertNode', $node);
            }
        },

        /**
         * Shows modal window
         *
         * @return  {void}
         */
        openAssetsManager() {
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

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(() => {
            $(this.$el).find('.close-footer input[type="checkbox"]').uniform();

            this.initializeRedactor();

            this.initEventBusListener();

            this.initializeAssetManager();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        $(this.$el).find(`#${this.id}`).redactor('core.destroy');
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.$forceUpdate();

                this.$redactorContainer.val(this.value);

                this.initializeRedactor();

                this.initializeAssetManager();
            }
        },
    },
};
