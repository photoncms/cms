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
                'image-alignment': 'Image alignment',
                'image-size': 'Image size',
                'insert': 'Insert',
                'save': 'Save',
                'source': 'Source',
                'form-title': 'Title',
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

            this.selection = app.selection;

            this.detector = app.detector;

            this.insertion = app.insertion;

            this.modalOptions = {
                title: 'Edit Image',
                name: 'photonImage',
                commands: {
                    insert: { title: 'Save' },
                    cancel: { title: 'Cancel' },
                    remove: { title: 'Delete', type: 'danger' },
                }
            };
        },

        // messages
        onmodal: {
            photonImage: {
                open: function($modal, $form)
                {
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

                            const $source = $photonImageCodeSnippet.find('span');

                            let photonImageSizeDropdownOptions = '';

                            if (!_.isEmpty(imageSizes)) {
                                imageSizes.forEach(imageSize => {
                                    photonImageSizeDropdownOptions += `<option value="${imageSize.id}">${imageSize.anchor_text}</option>`;
                                });
                            }

                            $('#photon-image-size').html(photonImageSizeDropdownOptions);

                            $form.setData({ 'photon-asset-id': assetId });

                            $form.setData({ 'photon-file_url': fileUrl });

                            $('#photon-image-preview').html($('<img src="' + $image.attr('src') + '" style="max-width: 100%;">'));

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

                                this.app.api('module.modal.close');

                                this.opts.openAssetsManager();
                            });

                        })
                        .catch((response) => {
                            pError('Failed to load values for image sizes dropdown from the API.', response);
                        });
                },
                opened: function()
                {
                    // this.app.api('insertion.insertHtml', 'A');

                    // setTimeout(() => {
                    //     this.app.api('insertion.insertHtml', 'B');
                    // }, 1000);

                    // if (this.detector.isDesktop()) {
                    //     $('#photon-image-title').focus();
                    // }
                },
                insert: function($modal, $form) {
                    this.save(this.node, $form);
                },
                remove: function()
                {
                    this.remove(this.node);
                },
            }
        },

        /**
         * Reveals the modal window
         *
         * @param   {event}  event
         * @return  {void}
         */
        show (node) {
            this.node = node;

            this.app.api('module.modal.build', this.modalOptions);
        },

        start: function()
        {
            var data = {
                title: 'Upload Image',
                api: 'plugin.editPhotonImage.open'
            };

            var $button = this.toolbar.addButton('editPhotonImage', data);

            $button.setIcon('<i class="fa fa-picture-o"></i>');
        },

        open: function() {
            this.selection.save();

            this.opts.openAssetsManager();

            // open the modal with API
            // this.app.api('module.modal.build', this.modalOptions);
        },

        modals: {
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
         * @return  {[type]}  [description]
         */
        remove ($photonImageCodeSnippet) {
            $photonImageCodeSnippet.remove();

            this.app.api('module.modal.close');

            this.app.api('module.source.sync');
        },

        /**
         * Performs the update of the code as per parameters set in the modal window
         *
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

                    $R.dom($photonImageCodeSnippet).html(template);

                    // this.app.api('insertion.insertHtml', template);

                    this.app.api('module.modal.close');

                    this.app.api('module.source.sync');

                    this.app.api('editor.focus');
                })
                .catch((response) => {
                    pError('Failed to load asset details from the API.', response);
                });
        },

        // messages
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

        oncontextbar: function(e, contextbar)
        {
            const data = this.inspector.parse(e.target);

            if (!data.isFigcaption()
                && data.isComponentType('widget')
                && $(e.target).data('widget-type') === 'photon-image')
            {
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
