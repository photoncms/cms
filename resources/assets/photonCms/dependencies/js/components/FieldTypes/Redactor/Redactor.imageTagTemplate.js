import _ from 'lodash';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

export const imageTagTemplate = function imageTagTemplate (asset) {

    // return '<figure class="photon-image-container" data-widget-type="photon-image"><div class="article-photo"><img src="http://photoncms.test/storage/assets/IMG_8162_960x640.JPG" alt="asdf" data-image-alignment="article-photo" data-image-size-id="2" data-asset-id="1" data-image="mdowrjiaremk"><span class="source"></span></div></figure>';

    const source = (_.has(asset, 'source.anchor_text') && asset.source.anchor_text) ? asset.source.anchor_text : '';

    const imageSizeId = (_.has(asset, 'imageSizeId') && asset.imageSizeId) ? asset.imageSizeId : redactorConfig.defaultImageSizeId;

    const title = (_.has(asset, 'title') && asset.title) ? asset.title : 'asdf';

    const imageAlignment = (_.has(asset, 'imageAlignment') && asset.imageAlignment) ? asset.imageAlignment : redactorConfig.defaultImageAlignment;

    const resizedImage = _.find(asset.resized_images, { image_size: parseInt(imageSizeId) });

    return `<div class="${imageAlignment}"><img src="${resizedImage.file_url}" alt="${title}" data-image-alignment="${imageAlignment}"  data-image-size-id="${imageSizeId}" data-asset-id="${asset.id}"><span class="source">${source}</span></div>`;
};
