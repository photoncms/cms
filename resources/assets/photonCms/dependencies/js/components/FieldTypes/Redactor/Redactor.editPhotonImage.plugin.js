import _ from 'lodash';

import { api } from '_/services/api';

import { config } from '_/config/config';

import { imageTagTemplate } from '~/components/FieldTypes/Redactor/Redactor.imageTagTemplate';

import { pError } from '_/helpers/logger';

(function($R) {
    $R.add('plugin', 'editPhotonImage', {
        /**
         * Language object, to be used with Redactor native translation system
         *
         * @type  {Object}
         */
        translations: {
            en: {
                'align-center': 'Align center',
                'align-left': 'Align left',
                'align-right': 'Align right',
                'cancel': 'Cancel',
                'change-photo': 'Change photo',
                'delete': 'Delete',
                'edit-image': 'Edit Image',
                'form-title': 'Title',
                'image-alignment': 'Image alignment',
                'image-size': 'Image size',
                'insert': 'Insert',
                'save': 'Save',
                'source': 'Source',
                'upload-image': 'Upload Image',
            },
        },

        init: function(app)
        {
            this.app = app;

            this.inspector = app.inspector;

            this.opts = app.opts;

            this.lang = app.lang;

            this.editor = app.editor;

            this.toolbar = app.toolbar;

            this.module = app.module;

            this.selection = app.selection;

            this.detector = app.detector;

            this.insertion = app.insertion;

            this.modalOptions = {
                name: 'photonImage',
                commands: {
                    cancel: { title: this.lang.get('cancel') },
                    insert: { title: this.lang.get('save') },
                    remove: {
                        title: this.lang.get('delete'),
                        type: 'danger'
                    },
                },
                title: this.lang.get('edit-image'),
            };
        },

        onmodal: {
            photonImage: {
                /**
                 * On Photon Image modal open event
                 *
                 * @param   {Object}  $modal
                 * @param   {Object}  $form
                 * @return  {void}
                 */
                open: function ($modal, $form) {
                    api.post(`${config.ENV.apiBasePath}/filter/image_sizes`, { include_relations: false })
                        .then(response => {
                            const imageSizes = response.body.body.entries;

                            const $image = $(this.node).find('img');

                            const fileUrl = $image.attr('src');

                            const title = $image.attr('alt');

                            const assetId = $image.data('assetId');

                            const imageSizeId = $image.data('imageSizeId');

                            const imageAlignment = $image.data('imageAlignment');

                            const $photonImageCodeSnippet = $image.parent();

                            const $source = $photonImageCodeSnippet.find('div.source');

                            let photonImageSizeDropdownOptions = '';

                            if (!_.isEmpty(imageSizes)) {
                                imageSizes.forEach(imageSize => {
                                    photonImageSizeDropdownOptions
                                        += `<option value="${imageSize.id}">${imageSize.anchor_text}</option>`;
                                });
                            }

                            $('#photon-image-size').html(photonImageSizeDropdownOptions);

                            $form.setData({ 'photon-asset-id': assetId });

                            $form.setData({ 'photon-file_url': fileUrl });

                            $('#photon-image-preview').html(
                                    $('<img src="' + $image.attr('src') + '" style="max-width: 100%;">')
                                );

                            $form.setData({ 'photon-image-size': imageSizeId });

                            $form.setData({ 'photon-image-alignment': imageAlignment });

                            if (title !== 'null') {
                                $form.setData({ 'photon-image-title': title });
                            }

                            if ($source) {
                                $form.setData({ 'photon-image-source': $source.text() });
                            }

                            $('#redactor-modal-button-change-photo').on('click', (event) => {
                                event.preventDefault();

                                this.opts.openAssetsManager();

                                $($form.nodes[0]).off('assetChanged')
                                    .on('assetChanged', (event, asset) => {
                                        const data = $form.getData();

                                        $form.setData({ 'photon-asset-id': asset.id });

                                        const previewUrl = _.find(asset.resized_images, { image_size: parseInt(data['photon-image-size']) });

                                        $form.setData({ 'photon-file_url': previewUrl.file_url });

                                        $('#photon-image-preview img').attr('src', previewUrl.file_url);
                                    });
                            });
                        })
                        .catch((response) => {
                            pError('Failed to load values for image sizes dropdown from the API.', response);
                        });
                },

                /**
                 * On Photon Image modal opened event
                 *
                 * @return  {void}
                 */
                opened: function()
                {
                    if (this.detector.isDesktop()) {
                        $('#photon-image-title').focus();
                    }
                },

                /**
                 * On Photon Image modal insert event
                 *
                 * @param   {Object}  $modal
                 * @param   {Object}  $form
                 * @return  {void}
                 */
                insert: function($modal, $form) {
                    this.save(this.node, $form);
                },

                /**
                 * On Photon Image modal remove event
                 *
                 * @param   {Object}  $modal
                 * @param   {Object}  $form
                 * @return  {void}
                 */
                remove: function()
                {
                    this.remove(this.node);
                },
            }
        },

        /**
         * Reveals the modal window
         *
         * @param   {Object}  node
         * @return  {void}
         */
        show (node) {
            this.node = node;

            this.app.api('module.modal.build', this.modalOptions);
        },

        /**
         * This method will launch at the same time as Redactor
         *
         * @return  {void}
         */
        start: function() {
            const data = {
                title: this.lang.get('upload-image'),
                api: 'plugin.editPhotonImage.open',
                observe: 'asset'
            };

            const $button = this.toolbar.addButton('asset', data);

            $button.setIcon('<i class="fa fa-picture-o"></i>');
        },


        /**
         * Initated on toolbar button click
         *
         * @return  {void}
         */
        open: function() {
            this.selection.save();

            this.opts.openAssetsManager();
        },

        onbutton: {
            asset: {
                observe: function(button)
                {
                    this.observeButton(button);
                }
            }
        },

        /**
         * Observes the Redactor selection and disables the button in given circumstances
         *
         * @param   {Object}  button
         * @return  {void}
         */
        observeButton: function(button)
        {
            var current = this.selection.getCurrent();
            var data = this.inspector.parse(current);

            if (data.isComponentType('widget')
                || data.isComponentType('video')) {
                return button.disable();
            }

            if (!this.selection.isCollapsed()) {
                return button.disable();
            }

            button.enable();
        },

        modals: {
            /**
             * Renders the modal form HTML
             *
             * @param   {array}  imageSizes
             * @return  {string}
             */
            photonImage: (function (imageSizes) {
                let photonImageSizeDropdownOptions = '';

                if (!_.isEmpty(imageSizes)) {
                    this.editPhotonImage.imageSizes.forEach(imageSize => {
                        photonImageSizeDropdownOptions += `<option value="${imageSize.id}">${imageSize.anchor_text}</option>`;
                    });
                }

                let photonImageAlignmentOptions = '<option value="article-photo pull-left">## align-left ##</option>';

                photonImageAlignmentOptions += '<option value="article-photo" selected>## center ##</option>';

                photonImageAlignmentOptions += '<option value="article-photo pull-right">## align-right ##</option>';

                return String()
                + '<form action="">'
                    + '<div class="redactor-modal-tab redactor-group" data-title="General">'
                        + '<div id="photon-image-preview" class="redactor-modal-tab-side">'
                        + '</div>'
                        + '<div class="redactor-modal-tab-area">'
                            + '<input type="hidden" id="photon-asset-id" name="photon-asset-id" value=""/>'
                            + '<input type="hidden" id="photon-file_url" name="photon-file_url" value=""/>'
                            + '<div class="form-item">'
                                + '<button id="redactor-modal-button-change-photo">## change-photo ##</button>'
                            + '</div>'
                            + '<div class="form-item">'
                                + '<label class="redactor-image-position-option">## image-size ##</label>'
                                + '<select class="redactor-image-position-option" id="photon-image-size" name="photon-image-size" aria-label="## image-size ##">'
                                    + photonImageSizeDropdownOptions
                                + '</select>'
                            + '</div>'
                            + '<div class="form-item">'
                                + '<label class="redactor-image-position-option">## image-alignment ##</label>'
                                + '<select class="redactor-image-position-option" id="photon-image-alignment" name="photon-image-alignment" aria-label="## image-alignment ##">'
                                    + photonImageAlignmentOptions
                                + '</select>'
                            + '</div>'
                            + '<div class="form-item">'
                                + '<label>## form-title ##</label>'
                                + '<input type="text" id="photon-image-title" name="photon-image-title" />'
                            + '</div>'
                            + '<div class="form-item">'
                                + '<label>## source ##</label>'
                                + '<input type="text" id="photon-image-source" name="photon-image-source" />'
                            + '</div>'
                        + '</div>'
                    + '</div>'
                + '</form>';
            })(),
        },

        /**
         * Stores available image sizes objects
         *
         * @type  {Array}
         */
        imageSizes: [],

        /**
         * Removes the image from the editor HTML
         *
         * @param   {object}  $photonImageCodeSnippet
         * @return  {void}
         */
        remove ($photonImageCodeSnippet) {
            $photonImageCodeSnippet.remove();

            this.app.api('module.modal.close');

            this.module.source.sync();
        },

        /**
         * Performs the update of the code as per parameters set in the modal window
         *
         * @param   {object}  $photonImageCodeSnippet
         * @param   {object}  $form
         * @return  {void}
         */
        save ($photonImageCodeSnippet, $form) {
            const assetId = $('#photon-asset-id').val();

            api.get(`assets/${assetId}`)
                .then(response => {
                    let asset = response.body.body.entry;

                    const data = $form.getData();

                    asset['imageSizeId'] = data['photon-image-size'].trim();

                    asset['imageAlignment'] = data['photon-image-alignment'].trim();

                    asset['source'] = {
                        anchor_text: data['photon-image-source'].trim(),
                    };

                    asset['title'] = data['photon-image-title'].trim();

                    const template = imageTagTemplate(asset);

                    $R.dom($photonImageCodeSnippet).html($R.dom(template));

                    $R.dom($photonImageCodeSnippet).attr(
                            'data-widget-code',
                            encodeURI($R.dom($photonImageCodeSnippet).nodes[0].innerHTML.trim())
                        );

                    this.app.api('module.modal.close');

                    this.app.api('editor.focus');
                })
                .catch((response) => {
                    pError('Failed to load asset details from the API.', response);
                });
        },

        /**
         * Triggered after HTML is inserted via Insertion API
         *
         * @param   {array}  nodes
         * @return  {void}
         */
        oninserted: function(nodes)
        {
            if(nodes.length > 0) {
                const node = nodes[0];

                if($(node).data('open-first')) {
                    $(node).removeData('open-first')
                        .removeAttr('data-open-first');

                    this.show(node);
                }
            }
        },

        /**
         * Triggered after context bar is invoked
         *
         * @param   {event}  e
         * @param   {object}  contextbar
         * @return  {void}
         */
        oncontextbar: function(e, contextbar) {
            const data = this.inspector.parse(e.target);

            if (!data.isFigcaption()
                && data.isComponentType('widget')
                && $(e.target).data('widget-type') === 'photon-image') {
                const node = data.getComponent();

                const buttons = {
                    'edit': {
                        title: this.lang.get('edit-image'),
                        api: 'plugin.editPhotonImage.show',
                        args: node
                    },
                    'remove': {
                        title: this.lang.get('delete'),
                        api: 'plugin.editPhotonImage.remove',
                        args: node
                    }
                };

                contextbar.set(e, node, buttons, 'bottom');
            }
        },
    });
})(Redactor);
