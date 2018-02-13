import Vue from 'vue';

Vue.filter('titleCase', function(value) {
    // Coverts to title case
    return value.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
});
