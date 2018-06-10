import _ from 'lodash';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

/**
 * Generates a Photon-image widget template code
 *
 * @param   {Object}  asset
 * @return  {Object}
 */
export const imageTagTemplate = function imageTagTemplate (asset) {
    const source = (_.has(asset, 'source.anchor_text') && asset.source.anchor_text) ? asset.source.anchor_text : '';

    const imageSizeId = (_.has(asset, 'imageSizeId') && asset.imageSizeId) ? asset.imageSizeId : redactorConfig.defaultImageSizeId;

    const title = (_.has(asset, 'title') && asset.title) ? asset.title : '';

    const imageAlignment = (_.has(asset, 'imageAlignment') && asset.imageAlignment) ? asset.imageAlignment : redactorConfig.defaultImageAlignment;

    const resizedImage = _.find(asset.resized_images, { image_size: parseInt(imageSizeId) });

    let $image = $R.dom('<img>')
        .attr('src', resizedImage.file_url)
        .attr('alt', title)
        .data('image-alignment', imageAlignment)
        .data('image-size-id', imageSizeId)
        .data('asset-id', asset.id);

    let $source = $R.dom('<div>')
        .addClass('source')
        .html(source);

    let $container = $R.dom('<div>')
        .addClass(imageAlignment)
        .append($image)
        .append($source);

    return $container;
};
