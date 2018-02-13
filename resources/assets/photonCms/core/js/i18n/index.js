import Vue from 'vue';

import VueI18n from 'vue-i18n';

const photonConfig = require('~/config/config.json');

Vue.use(VueI18n);

const i18n = new VueI18n({
    locale: photonConfig.locale,
    messages: {
        'en': require('./en.json'),
        'sr': require('./sr.json')
    }
});

if (module.hot) {
    module.hot.accept(['./en.json', './sr.json'], () => {
        i18n.setLocaleMessage('en', require('./en.json'));
        i18n.setLocaleMessage('sr', require('./sr.json'));
    });
}

export default i18n;
