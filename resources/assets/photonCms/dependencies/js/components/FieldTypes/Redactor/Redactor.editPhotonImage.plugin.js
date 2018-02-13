import _ from 'lodash';

import { api } from '_/services/api';

import { config } from '_/config/config';

import { imageTagTemplate } from '~/components/FieldTypes/Redactor/Redactor.imageTagTemplate';

import { pError } from '_/helpers/logger';

(function($) {
    $.Redactor.prototype.editPhotonImage = function() {
        return {
            /**
             * Defines the template for the modal window
             *
             * @param   {array}  imageSizes
             * @return  {string}
             */
            getTemplate (imageSizes) {
                let photonImageSizeDropdownOptions = '';

                if (!_.isEmpty(imageSizes)) {
                    this.editPhotonImage.imageSizes.forEach(imageSize => {
                        photonImageSizeDropdownOptions += `<option value="${imageSize.id}">${imageSize.anchor_text}</option>`;
                    });
                }

                let photonImageAlignmentOptions = `<option value="article-photo pull-left">${this.lang.get('align-left')}</option>`;

                photonImageAlignmentOptions += `<option value="article-photo" selected>${this.lang.get('center')}</option>`;

                photonImageAlignmentOptions += `<option value="article-photo pull-right">${this.lang.get('align-right')}</option>`;

                return String()
                + '<div class="redactor-modal-tab redactor-group" data-title="General">'
                    + '<div id="photon-image-preview" class="redactor-modal-tab-side">'
                    + '</div>'
                    + '<div class="redactor-modal-tab-area">'
                        + '<input type="hidden" id="photon-asset-id" value=""/>'
                        + '<input type="hidden" id="photon-file_url" value=""/>'
                        + '<section>'
                            + '<button id="redactor-modal-button-change-photo">' + this.lang.get('change-photo') + '</button>'
                        + '</section>'
                        + '<section>'
                            + '<label class="redactor-image-position-option">' + this.lang.get('image-size') + '</label>'
                            + '<select class="redactor-image-position-option" id="photon-image-size" aria-label="' + this.lang.get('image-size') + '">'
                                + photonImageSizeDropdownOptions
                            + '</select>'
                        + '</section>'
                        + '<section>'
                            + '<label class="redactor-image-position-option">' + this.lang.get('image-alignment') + '</label>'
                            + '<select class="redactor-image-position-option" id="photon-image-alignment" aria-label="' + this.lang.get('image-alignment') + '">'
                                + photonImageAlignmentOptions
                            + '</select>'
                        + '</section>'
                        + '<section>'
                            + '<label>' + this.lang.get('title') + '</label>'
                            + '<input type="text" id="photon-image-title" />'
                        + '</section>'
                        + '<section>'
                            + '<label>' + this.lang.get('source') + '</label>'
                            + '<input type="text" id="photon-image-source" />'
                        + '</section>'
                        + '<section>'
                            + '<button id="redactor-modal-button-save">' + this.lang.get('save') + '</button>'
                            + '<button id="redactor-modal-button-cancel">' + this.lang.get('cancel') + '</button>'
                            + '<button id="redactor-modal-button-delete" class="redactor-modal-button-offset">' + this.lang.get('delete') + '</button>'
                        + '</section>'
                    + '</div>'
                + '</div>';
            },

            /**
             * Stores available image sizes objects
             *
             * @type  {Array}
             */
            imageSizes: [],

            /**
             * Redactor plugin Init method, ran as the constructor
             *
             * @return  {void}
             */
            init () {
                $(this.$editor).click(event => {
                    if (!$(event.target).is('img')) {
                        return;
                    }

                    api.post(`${config.ENV.apiBasePath}/filter/image_sizes`, { include_relations: false })
                        .then(response => {
                            this.editPhotonImage.imageSizes = response.body.body.entries;

                            this.editPhotonImage.show(event, this.editPhotonImage.imageSizes);
                        })
                        .catch((response) => {
                            pError('Failed to load values for image sizes dropdown from the API.', response);
                        });
                });
            },

            /**
             * Removes the image from the editor HTML
             *
             * @return  {[type]}  [description]
             */
            remove ($photonImageCodeSnippet) {
                $photonImageCodeSnippet.remove();

                this.modal.close();

                this.buffer.set();

                this.code.sync();
            },

            /**
             * Performs the update of the code as per parameters set in the modal window
             *
             * @return  {void}
             */
            save ($photonImageCodeSnippet) {
                const assetId = $('#photon-asset-id').val();

                api.get(`assets/${assetId}`)
                    .then(response => {
                        let asset = response.body.body.entry;

                        asset['imageSizeId'] = $('#photon-image-size').val();

                        asset['imageAlignment'] = $('#photon-image-alignment').val();

                        asset['source'] = {
                            anchor_text: $('#photon-image-source').val(),
                        };

                        asset['title'] = $('#photon-image-title').val();

                        const template = imageTagTemplate(asset);

                        $photonImageCodeSnippet.replaceWith(template);

                        this.modal.close();

                        this.buffer.set();

                        this.code.sync();
                    })
                    .catch((response) => {
                        pError('Failed to load asset details from the API.', response);
                    });
            },

            /**
             * Reveals the modal window
             *
             * @param   {event}  event
             * @param   {array}  imageSizes
             * @return  {void}
             */
            show (event, imageSizes) {
                const $image = $(event.target);

                const fileUrl = $image.attr('src');

                const title = $image.attr('alt');

                const assetId = $image.data('assetId');

                const imageSizeId = $image.data('imageSizeId');

                const imageAlignment = $image.data('imageAlignment');

                const $photonImageCodeSnippet = $image.parent();

                const $source = $photonImageCodeSnippet.find('span');

                this.modal.addTemplate('editPhotonImage', this.editPhotonImage.getTemplate(imageSizes));

                this.modal.load('editPhotonImage', this.lang.get('edit-image'), 705);

                $('#photon-asset-id').val(assetId);

                $('#photon-file_url').val(fileUrl);

                $('#photon-image-preview').html($('<img src="' + $image.attr('src') + '" style="max-width: 100%;">'));

                $('#photon-image-size').val(imageSizeId);

                $('#photon-image-alignment').val(imageAlignment);

                if (title !== 'undefined') {
                    $('#photon-image-title').val(title);
                }

                if ($source) {
                    $('#photon-image-source').val($source.text());
                }

                $('#redactor-modal-button-save').on('click', () => {
                    this.editPhotonImage.save($photonImageCodeSnippet);
                });

                $('#redactor-modal-button-delete').on('click', () => {
                    this.editPhotonImage.remove($photonImageCodeSnippet);
                });

                $('#redactor-modal-button-change-photo').on('click', () => {
                    this.opts.Vue.openAssetsManager();
                });

                this.modal.show();

                if (this.detect.isDesktop()) {
                    $('#photon-image-title').focus();
                }
            },

            /**
             * Language object, to be used with Redactor native translation system
             *
             * @type  {Object}
             */
            langs: {
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
                    'title': 'Title',
                }
            },

        };
    };
})(jQuery);
