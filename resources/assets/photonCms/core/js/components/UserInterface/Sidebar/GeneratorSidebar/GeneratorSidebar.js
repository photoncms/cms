import { mapGetters } from 'vuex';

import _ from 'lodash';

import {
    destroyJsTree,
    jsTreeReselectNode,
    setupJsTree,
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
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'generator.selectedModule' (newEntry, oldEntry) {
            const newId = _.has(newEntry, 'id') ? newEntry.id : null;

            const oldId = _.has(oldEntry, 'id') ? oldEntry.id : null;

            if (newId !== oldId) {
                jsTreeReselectNode();
            }
        }
    },
};
