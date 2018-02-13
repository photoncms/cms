import { mapGetters } from 'vuex';

import { pWarn } from '_/helpers/logger';

import {
    destroyJsTree,
    jsTreeReselectNode,
    refreshJsTree,
    setupJsTree,
} from '_/components/UserInterface/Sidebar/MenuEditorSidebar/MenuEditorSidebar.jsTree';

import Vue from 'vue';

const QuickSearch = Vue.component(
        'QuickSearch',
        require('_/components/UserInterface/Sidebar/MenuEditorSidebar/QuickSearch/QuickSearch.vue')
    );

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            /**
             * Stores all the nodes loaded by the jsTree
             *
             * @type  {Array}
             */
            nodes: [],
        };
    },

    /**
     * Setup Components
     *
     * @type  {Object}
     */
    components: {
        QuickSearch,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        // Map getters
        ...mapGetters({
            menuEditor: 'menuEditor/menuEditor',
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
            setupJsTree(this);
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
        $route (newEntry, oldEntry) {
            pWarn(
                'Watched property fired.',
                'AdminSidebar',
                '$route',
                newEntry,
                oldEntry,
                this
            );

            const menuId = newEntry.params.menuId;

            const oldMenuId = oldEntry.params.menuId;

            // If menu id has changed
            if (menuId !== oldMenuId) {
                pWarn('MenuEditorSidebar.js', 'Menu Id Changed');
                jsTreeReselectNode(menuId);

                return;
            }

            pWarn('MenuEditorSidebar.js', 'Normal Refresh');
            // Else refresh normally
            refreshJsTree();
        },

        'menuEditor.submitInProgress' (newEntry, oldEntry) {
            if (newEntry === false) {
                pWarn(
                    'Watched property fired.',
                    'MenuEditorSidebar',
                    'menuEditor.submitInProgress',
                    newEntry,
                    oldEntry,
                    this
                );

                destroyJsTree();

                setupJsTree(this);
            }
        },
    },
};
