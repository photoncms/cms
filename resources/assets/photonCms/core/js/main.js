import Vue from 'vue';
import VueResource from 'vue-resource';
import VueI18n from 'vue-i18n';
import { store } from '_/vuex/store';
import { storage } from '_/services/storage';
import { api } from '_/services/api';
import { router } from '_/router/router';
import i18n from '_/i18n';

Vue.use(VueResource);

Vue.use(VueI18n);

api.bootstrap(); // Bootstrap the API service

storage.bootstrap(); // Bootstrap storage from session or local storage

/**
 * Import the App component
 *
 * @type {object}
 */
const App = require('_/components/App/App.vue');

// Set some custom filters
require('_/filters/filter.toString');
require('_/filters/filter.separateWords');
require('_/filters/filter.titleCase');

new Vue({ // Start the vuex storage enabled app
    i18n,
    store,
    el: '#app',
    router: router,
    components: { App }
});
