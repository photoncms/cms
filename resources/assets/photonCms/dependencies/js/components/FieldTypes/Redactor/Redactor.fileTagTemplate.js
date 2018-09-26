import _ from 'lodash';

import { redactorConfig } from '~/components/FieldTypes/Redactor/Redactor.config';

export const fileTagTemplate = function fileTagTemplate (asset, tempId) {
    return `<a href="${asset.file_url}" title="${asset.title}">${asset.file_name}</a>`;
};
