import { api } from '_/services/api';

import { config } from '_/config/config';

import { router } from '_/router/router';

import {
    pError,
    pWarn,
} from '_/helpers/logger';

import { store } from '_/vuex/store';

import nodeDoubleClickHandler from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.nodeDoubleClickHandler';

import nodeSelectHandler from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.nodeSelectHandler';

import nodeMoveHandler from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.nodeMoveHandler';

export let jsTreeInstance;

/**
 * Sets up jsTree instance
 *
 * @return  {jstreeinstance}
 */
export const setupJsTree = (vueComponent) => {
    $('#tree').jstree({
        core: {
             /**
              * Determines what happens when a user tries to modify the structure of the tree.
              * If left as false all operations like create, rename, delete, move or copy are prevented.
              */
            check_callback: function (operation, node, node_parent, node_position, more) {
                if (operation === 'move_node') {
                    /**
                     * Get max depth at which items can be placed for the currently edited menu
                     *
                     * @type  {integer}
                     */
                    const maxDepth = store.state.menuItemsEditor.selectedMenu.max_depth;

                    // If max depth is null, any type of nesting is allowed
                    if (maxDepth === null) {
                        return true;
                    }

                    /**
                     * Store the depth of the currently moved node
                     *
                     * @type  {integer}
                     */
                    let deepestChildItem = node.original.depth;

                    /**
                     * Map through the children_d jsTree object (representing child items of the moved node)
                     *
                     * @param   {integer}  (childItemId)
                     * @return  {array}
                     */
                    node.children_d.map((childItemId) => {
                        // For each child item get the corresponding menu item object from store
                        const matchingMenuItem = vueComponent.nodes
                            .find((menuItem) => menuItem.id === parseInt(childItemId, 10));

                        /**
                         * If current child item depth is deeper than the last itterated child item depth
                         * replace the deepestChildItem with it's value.
                         * At the end of the map loop this yield the deepest child depth
                         * (or if no children, the deepestChildItem remains at the depth of the moved node)
                         */
                        deepestChildItem = Math.max(deepestChildItem, matchingMenuItem.depth);
                    });

                    /**
                     * Calculate depth relative to the currently moved node's depth
                     *
                     * @type  {integer}
                     */
                    const relativeDepth = deepestChildItem - node.original.depth;

                    // Get the node the current node is being dropped into (can also be root)
                    const newParent = jsTreeInstance.get_node(node_parent.id);

                    // If placing in root the new position depth will be 0,
                    // else the depth will be one lower than the parent the node is being nested in
                    const newParentDepth = newParent.original ? (newParent.original.depth + 1) : 0;

                    // Check if the new depth will exceed the max allowed depth
                    if (maxDepth - newParentDepth < relativeDepth) {
                        pWarn('Trying to move item (or one of it\'s child items) deeper than the max depth limit for this menu (', maxDepth, ')');

                        // False meaning move not allowed
                        return false;
                    }

                    return true;
                }

                return false;
            },

            /**
             * The function will receive the node being loaded as argument and a second param which is a
             * function that should be called with the result.
             */
            data: function(node, jsTreeCallback) {
                const uri = `${config.ENV.apiBasePath}/menus/${router.currentRoute.params.menuId}/items`;

                api.get(uri)
                    .then((response) => {
                        const entries = response.body.body.menu_items;

                        let rootNodesCount = 0;

                        const nodes = entries.map(function(node){
                            if (!node.parent_id) {
                                rootNodesCount += 1;
                            }

                            return {
                                a_attr: rootNodesCount > 3 ? { class: 'warn' } : '',
                                children: false,
                                depth: node.depth,
                                id: node.id,
                                parent: node.parent_id ? node.parent_id : '#',
                                text: node.title,
                            };
                        });

                        vueComponent.nodes = nodes;

                        jsTreeCallback.call(this, nodes);
                    })
                    .catch((response) => {
                        pError(`Failed to load values for jsTree node ${node.id}`, response);
                    });
            },

            // A boolean indicating if multiple nodes can be selected
            multiple: false,

            // Theme configuration object
            themes: {
                name: 'proton',
                responsive: true,
            },

            // Should the node should be toggled if the text is double clicked. Defaults to true
            dblclick_toggle: false,
        },

        // Stores all loaded jstree plugins
        plugins: [
            'conditionalselect',
            'dnd',
            'state',
            'types',
        ],

        /**
         * Saves the state of the tree (selected nodes, opened nodes) on the user's computer using available options
         * (localStorage, cookies, etc)
         */
        state: {
            /**
             * A function that will be executed prior to restoring state with one argument - the state object.
             * Can be used to clear unwanted parts of the state.
             */
            filter : function (state) {
                /**
                 * If we're editing the entry, allow load_node action to fire. If state returns it's dta the load_node
                 * action won't fire meaning that currently edited entry likely won't be opened and highlighted.
                 */
                if (store.state.menuItemsEditor.editorMode === 'edit') {
                    return;
                }

                delete state.core.selected;

                return state;
            }
        },

        /**
         * This plugin overrides the activate_node function (the one that gets called
         * when a user tries to select a node) and enables preventing the function invokation by using a callback.
         *
         * @return  {boolean}
         */
        conditionalselect: function() {
            if(store.state.menuItemsEditor.editorMode === 'edit') {
                return false;
            }

            return true;
        },

        // This plugin makes it possible to drag and drop tree nodes and rearrange the tree.
        dnd: {
            // a boolean indicating if a copy should be possible while dragging (by pressint the meta key or Ctrl).
            copy: false,

            /**
             * When starting the drag on a node that is selected this setting controls if all selected nodes
             * are dragged or only the single node. Default is true, which means all selected nodes are dragged when
             * the drag is started on a selected node.
             */
            drag_selection: false,

            /**
             * When dropping a node "inside", this setting indicates the position the node should go to - it can be
             * an integer or a string: "first" (same as 0) or "last", default is 0
             */
            inside_pos: 'last',

            /**
             * A function invoked each time a node is about to be dragged, invoked in the tree's scope and receives
             * the nodes about to be dragged as an argument (array) and the event that started the drag - return
             * false to prevent dragging.
             */
            is_draggable : function() {
                return true;
            },

            // A number indicating how long a node should remain hovered while dragging to be opened. Defaults to 500.
            open_timeout: 300,

            /**
             * controls whether dnd works on touch devices. If left as boolean true dnd will work the same as in
             * desktop browsers, which in some cases may impair scrolling. If set to boolean false dnd will
             * not work on touch devices. There is a special third option - string "selected" which means
             * only selected nodes can be dragged on touch devices.
             */
            touch: 'selected',
        },
    })

    // When jstree is ready call onJsTreeReady handler
    .on('ready.jstree', onJsTreeReady)

    // When jstree is ready call onJsTreeReady handler
    .on('state_ready.jstree', () => {
        onAllNodesLoaded();
    })

    // When jstree refreshes call onJsTreeReady handler
    .on('refresh.jstree', onJsTreeReady)

    // When jstree changes (node selected) call nodeSelectHandler
    .on('changed.jstree', nodeSelectHandler)

    // When jstree node is moved call nodeMoveHandler
    .on('move_node.jstree', nodeMoveHandler)

    // When jstree node is moved call nodeMoveHandler
    .on('deselect_node.jstree', () => {
        if(store.state.menuItemsEditor.selectedModule.category) {
            store.dispatch('menuItemsEditor/changeEditorMode', { newEditorMode: 'default' });
        }

        store.dispatch('menuItemsEditor/updateSelectedNode', {
            anchorText: null,
            url: null,
        });

        store.dispatch('menuItemsEditor/createEntry', {});
    });

    // Assign jsTree instance to exported jsTreeInstance object and return it
    jsTreeInstance = $('#tree').jstree(true);

    return jsTreeInstance;
};

/**
 * Conducts a new node selection
 *
 * @param   {integer}  menuEntryId
 * @return  {void}
 */
export const jsTreeReselectNode = (menuEntryId) => {
    jsTreeInstance.deselect_all(true);

    jsTreeInstance.select_node(menuEntryId, true);
};

/**
 * On jsTree ready handler
 *
 * @return  {void}
 */
export const onJsTreeReady = () => {
    // Deselect any selected nodes
    jsTreeInstance.deselect_all(true);

    const entryId = store.state.menuItemsEditor.editedEntry.id;

    // In no entry is being edited
    if (!store.state.menuItemsEditor.editedEntry.id) {
        return;
    }

    jsTreeInstance.select_node(entryId, true);
};

const onAllNodesLoaded = () => {
    const entryId = store.state.menuItemsEditor.editedEntry.id;

    let hasNodes = true;

    if ($('#tree').find('li').length === 0) {
        hasNodes = false;
    }

    store.dispatch('ui/updateHasNodes', { hasNodes });

    $('#tree').off('dblclick', '.jstree-anchor');

    $('#tree').on('dblclick', '.jstree-anchor', nodeDoubleClickHandler);

    $('#tree').off('click', '.jstree-clicked');

    if (store.state.menuItemsEditor.editorMode === 'default' || store.state.menuItemsEditor.editorMode === 'create') {
        $('#tree').on('click', '.jstree-clicked', function () {
            $('#tree').jstree(true).deselect_node(this);
        });
    }

    jsTreeInstance.select_node(entryId, true);
};

/**
 * Expose jsTree refresh method
 *
 * @return  {void}
 */
export const refreshJsTree = () => {
    if (jsTreeInstance) {
        jsTreeInstance.refresh();
    }
};

/**
 * Expose jsTree destroy method
 *
 * @return  {void}
 */
export const destroyJsTree = () => {
    if (jsTreeInstance) {
        jsTreeInstance.destroy();
    }
};
