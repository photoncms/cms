import _ from 'lodash';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

export const imageTagTemplate = function imageTagTemplate (asset, tempId) {
    const source = (!_.isEmpty(asset.source)) ? asset.source.anchor_text : '';

    const imageSizeId = asset.imageSizeId ? asset.imageSizeId : redactorConfig.defaultImageSizeId;

    const imageAlignment = asset.imageAlignment ? asset.imageAlignment : redactorConfig.defaultImageAlignment;

    const resizedImage = _.find(asset.resized_images, { image_size: parseInt(imageSizeId) });

    return `<div class="${imageAlignment}"><img src="${resizedImage.file_url}" id="${tempId}" alt="${asset.title}" data-image-alignment="${imageAlignment}"  data-image-size-id="${imageSizeId}" data-asset-id="${asset.id}"><span class="source">${source}</span></div>`;
};
