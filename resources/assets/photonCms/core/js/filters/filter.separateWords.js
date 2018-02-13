import Vue from 'vue';

Vue.filter('separateWords', function(value) {
    // Separates words joined with - and/or _
    value = value.replace('_', ' ');
    value = value.replace('-', ' ');
    return value;
});