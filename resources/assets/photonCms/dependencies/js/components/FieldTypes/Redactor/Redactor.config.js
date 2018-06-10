import { clipsItems } from '~/components/FieldTypes/Redactor/Redactor.clips.plugin.items';

const config = require('~/config/config.json');

export const redactorConfig = {
    'buttons': [
        'html',
        'format',
        'bold',
        'italic',
        'lists',
        'link',
    ],
    'clips': clipsItems,
    'defaultImageAlignment': 'article-photo',
    'defaultImageSizeId': 2,
    'formatting': [
        'p',
        'blockquote',
        'pre',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
    ],
    'imageEditable': false,
    'imageFigure': true,
    'minHeight': '300px',
    'maxWidth': '660px',
    'plugins': [
        'alignment',
        'clips',
        'counter',
        'fontcolor',
        'fontsize',
        'fullscreen',
        'video',
        'widget',
        'editPhotonImage',
    ],
    'script': true,
    'source': true,
    'spellcheck': config.spellcheck,
};
