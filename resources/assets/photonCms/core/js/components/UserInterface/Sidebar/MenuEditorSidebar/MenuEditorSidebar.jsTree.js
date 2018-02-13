import { api } from '_/services/api';

import { config } from '_/config/config';

import { pError } from '_/helpers/logger';

import { store } from '_/vuex/store';

// Import handler functions
import nodeDoubleClickHandler from '_/components/UserInterface/Sidebar/MenuEditorSidebar/MenuEditorSidebar.nodeDoubleClickHandler';

import nodeSelectHandler from '_/components/UserInterface/Sidebar/MenuEditorSidebar/MenuEditorSidebar.nodeSelectHandler';

import nodeMoveHandler from '_/components/UserInterface/Sidebar/MenuEditorSidebar/MenuEditorSidebar.nodeMoveHandler';

export let jsTreeInstance;

/**
 * Sets up jsTree instance
 *
 * @return  {jstreeinstance}
 */
export const setupJsTree = (vueComponent) => {
    $('#tree').jstree({
        core: {
             // Determines what happens when a user tries to modify the structure of the tree.
             // If left as false all operations like create, rename, delete, move or copy are prevented.
            check_callback: function (operation, node, node_parent, node_position, more) {
                return false;
            },

            // The function will receive the node being loaded as argument and a second param which is a
            // function that should be called with the result.
            data: function(node, jsTreeCallback) {
                const uri = `${config.ENV.apiBasePath}/menus`;

                api.get(uri)
                    .then((response) => {
                        const entries = response.body.body.menus;

                        const nodes = entries.map(function(node){

                            return {
                                children: false,
                                id: node.id,
                                originalId: node.id,
                                text: node.title,
                            };
                        });

                        vueComponent.nodes = nodes;

                        jsTreeCallback.call(this, nodes);
                    })
                    .catch((response) => {
                        pError('Failed to load nodes.', response);
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
    })

    // When jstree is ready call onJsTreeReady handler
    .on('ready.jstree', onJsTreeReady)

    // When jstree is ready call onJsTreeReady handler
    .on('state_ready.jstree', function() {
        onAllNodesLoaded();
    })

    // When jstree refreshes call onJsTreeReady handler
    .on('refresh.jstree', onJsTreeReady)

    // When jstree changes (node selected) call nodeSelectHandler
    .on('changed.jstree', nodeSelectHandler)

    // When jstree node is moved call nodeMoveHandler
    .on('move_node.jstree', nodeMoveHandler);

    // Assign jsTree instance to exported jsTreeInstance object and return it
    jsTreeInstance = $('#tree').jstree(true);

    return jsTreeInstance;
};

/**
 * Conducts a new node selection
 *
 * @param   {integer}  menuId
 * @return  {void}
 */
export const jsTreeReselectNode = (menuId) => {
    jsTreeInstance.deselect_all(true);

    jsTreeInstance.select_node(menuId, true);
};

/**
 * On jsTree ready handler
 *
 * @return  {void}
 */
export const onJsTreeReady = () => {
    // Deselect any selected nodes
    jsTreeInstance.deselect_all(true);

    onAllNodesLoaded();
};

const onAllNodesLoaded = () => {
    const entryId = store.state.menuEditor.editedEntry.id;

    const tableName = 'menus';

    let hasNodes = true;

    if ($('#tree').find('li').length === 0) {
        hasNodes = false;
    }

    store.dispatch('ui/updateHasNodes', { hasNodes });

    $('#tree').off('dblclick', '.jstree-anchor');

    $('#tree').on('dblclick', '.jstree-anchor', nodeDoubleClickHandler);

    $('#tree').off('click', '.jstree-clicked');

    if (store.state.menuEditor.editorMode === 'default' || store.state.menuEditor.editorMode === 'create') {
        $('#tree').on('click', '.jstree-clicked', function () {
            $('#tree').jstree(true).deselect_node(this);
        });
    }

    jsTreeInstance.select_node(`${tableName}.${entryId}`, true);
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
