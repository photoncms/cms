import Vue from 'vue';

Vue.filter('toString', function(value) {
    // Converts value to string
    return String(value);
});