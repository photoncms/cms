import {
    mapGetters
} from 'vuex';

export default {
    /**
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        ui: 'ui/ui'
    }),
};
