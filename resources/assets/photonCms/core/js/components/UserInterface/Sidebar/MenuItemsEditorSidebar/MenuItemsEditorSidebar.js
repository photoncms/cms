import { mapGetters } from 'vuex';

import { pWarn } from '_/helpers/logger';

import {
    destroyJsTree,
    jsTreeReselectNode,
    refreshJsTree,
    setupJsTree,
} from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.jsTree';

import Vue from 'vue';

const MenuPicker = Vue.component(
        'MenuPicker',
        require('_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuPicker/MenuPicker.vue')
    );

const QuickSearch = Vue.component(
        'QuickSearch',
        require('_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/QuickSearch/QuickSearch.vue')
    );

export default {
    /**
     * Setup Components
     *
     * @type  {Object}
     */
    components: {
        MenuPicker,
        QuickSearch,
    },

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
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            menuItemsEditor: 'menuItemsEditor/menuItemsEditor',
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
                'MenuItemsEditorSidebar',
                '$route',
                newEntry,
                oldEntry,
                this
            );

            const menuId = newEntry.params.menuId;

            const oldMenuId = oldEntry.params.menuId;

            const menuItemId = newEntry.params.menuItemId;

            const oldMenuItemId = oldEntry.params.menuItemId;

            // If menu has changed
            if (menuId !== oldMenuId) {
                refreshJsTree();

                return;
            }

            // If menu item id has changed
            if (menuItemId !== oldMenuItemId) {
                jsTreeReselectNode(menuItemId);

                return;
            }

            pWarn('AdminSidebar.js', 'Normal Refresh');

            // Else refresh normally
            refreshJsTree();
        },

        'menuItemsEditor.submitInProgress' (newEntry, oldEntry) {
            if (newEntry === false) {
                pWarn(
                    'Watched property fired.',
                    'MenuItemsEditorSidebar',
                    'menuItemsEditor.submitInProgress',
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
