import { mapGetters } from 'vuex';

import {
    setupJsTree,
    destroyJsTree
} from '_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.jsTree';


export default {
    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            generator: 'generator/generator',
            ui: 'ui/ui',
        })
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            setupJsTree();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        destroyJsTree();
    }
};
