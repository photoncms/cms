import _ from 'lodash';

import { api } from '_/services/api';

import { config } from '_/config/config';

import { pError } from '_/helpers/logger';

import { store } from '_/vuex/store';

// Import handler functions
import nodeDoubleClickHandler from '_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.nodeDoubleClickHandler';

import nodeSelectHandler from '_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.nodeSelectHandler';

import nodeMoveHandler from '_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.nodeMoveHandler';

export let jsTreeInstance;

const apiNodesBasePath = `${config.ENV.apiBasePath}/nodes`;

let vueComponent = {};

/**
 * Sets up jsTree instance
 *
 * @return  {jstreeinstance}
 */
export const setupJsTree = (vueComponentReference) => {
    vueComponent = vueComponentReference;

    if (vueComponent.selectedModule.type == 'single_entry') {
        return;
    }

    $('#tree').jstree({
        core: {
             // Determines what happens when a user tries to modify the structure of the tree.
             // If left as false all operations like create, rename, delete, move or copy are prevented.
            check_callback: function (operation, node, node_parent, node_position, more) {
                if (operation === 'create_node') {
                    return true;
                }

                if (operation === 'move_node') {
                    if (more.pos !== 'i'
                            && more.dnd === true
                            && node.original.tableName !== more.ref.original.tableName) {
                        return false;
                    }

                    if(!(vueComponent.selectedModule.type === 'multilevel_sortable'
                    || vueComponent.selectedModule.type === 'sortable')) {
                        return false;
                    }

                    return true;
                }

                return false;
            },

            // The function will receive the node being loaded as argument and a second param which is a
            // function that should be called with the result.
            data: function(node, jsTreeCallback) {
                // Use filter endpoint for non_sortable module type
                if(vueComponent.selectedModule.type === 'non_sortable') {
                    // Execute the callback with an empty array
                    jsTreeCallback.call(this, []);

                    return;
                }


                // Else use node endpoint
                if(vueComponent.selectedModule.type === 'multilevel_sortable'
                    || vueComponent.selectedModule.type === 'sortable') {
                    const uri = node.id === '#'
                        ? `${apiNodesBasePath}/${nodeScopes[0].table_name}`
                        : `${apiNodesBasePath}/${node.original.tableName}/${node.original.originalId}`;

                    const payload = {
                        params: {
                            child_modules: validChildModules,
                        },
                    };

                    api.get(uri, payload)
                        .then((response) => {
                            const entries = response.body.body.nodes;

                            const nodes = entries.map(function(node){
                                return {
                                    children: node.has_children,
                                    id: `${node.table_name}.${node.id}`,
                                    originalId: node.id,
                                    tableName: node.table_name,
                                    scopeId: node.scope_id,
                                    text: node.anchor_text,
                                    type: node.table_name,
                                };
                            });

                            jsTreeCallback.call(this, nodes);
                        })
                        .catch((response) => {
                            pError(`Failed to load values for jsTree node ${node.id}`, response);
                        });
                }
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

        // Saves the state of the tree (selected nodes, opened nodes) on the user's computer using available options
        // (localStorage, cookies, etc)
        state: {
            // A function that will be executed prior to restoring state with one argument - the state object.
            // Can be used to clear unwanted parts of the state.
            filter : function (state) {
                // If we're editing the entry, allow load_node action to fire. If state returns it's dta the load_node
                // action won't fire meaning that currently edited entry likely won't be opened and highlighted.
                if (store.state.admin.editorMode === 'edit') {
                    return;
                }

                delete state.core.selected;

                return state;
            }
        },

        // This plugin overrides the activate_node function (the one that gets called
        // when a user tries to select a node) and enables preventing the function invokation by using a callback.
        conditionalselect: function(node) {
            if(store.state.admin.editorMode === 'edit') {
                return false;
            }

            const selectedNodeTypeRules = jsTreeInstance.get_rules(node);

            if(vueComponent.selectedModule.type === 'non_sortable') {
                return false;
            }

            // If selected node is edited modules parent module allow selection
            if(selectedNodeTypeRules.valid_children
                && selectedNodeTypeRules.valid_children.includes(vueComponent.selectedModule.table_name)) {
                return true;
            }

            if(vueComponent.selectedModule.type !== 'multilevel_sortable') {
                return false;
            }

            // If selected node is of edited modules type, allow selection
            if (node.type === vueComponent.selectedModule.table_name
                && !jsTreeInstance.is_selected(node)) {
                return true;
            }

            return false;
        },

        // This plugin makes it possible to drag and drop tree nodes and rearrange the tree.
        dnd: {
            // a boolean indicating if a copy should be possible while dragging (by pressint the meta key or Ctrl).
            copy: false,

            // When starting the drag on a node that is selected this setting controls if all selected nodes
            // are dragged or only the single node. Default is true, which means all selected nodes are dragged when
            // the drag is started on a selected node.
            drag_selection: false,

            // when dropping a node "inside", this setting indicates the position the node should go to - it can be
            // an integer or a string: "first" (same as 0) or "last", default is 0
            inside_pos: 'last',

            // A function invoked each time a node is about to be dragged, invoked in the tree's scope and receives
            // the nodes about to be dragged as an argument (array) and the event that started the drag - return
            // false to prevent dragging.
            is_draggable : function(node) {
                if (node[0].original.tableName === vueComponent.selectedModule.table_name) {
                    return true;
                }

                return false;
            },

            // A number indicating how long a node should remain hovered while dragging to be opened. Defaults to 500.
            open_timeout: 300,

            // controls whether dnd works on touch devices. If left as boolean true dnd will work the same as in
            // desktop browsers, which in some cases may impair scrolling. If set to boolean false dnd will
            // not work on touch devices. There is a special third option - string "selected" which means
            // only selected nodes can be dragged on touch devices.
            touch: 'selected',
        },

        // An object storing all types as key value pairs, where the key is the type name and the value is
        // an object that can contain optional keys.
        types: buildTypes(),

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
    .on('move_node.jstree', nodeMoveHandler)

    // When jstree node is moved call nodeMoveHandler
    .on('deselect_node.jstree', function(){
        if(vueComponent.selectedModule.category) {
            store.dispatch('admin/changeEditorMode', { newEditorMode: 'default' });
        }

        store.dispatch('admin/updateSelectedNode', {
            anchorText: null,
            url: null,
        });

        store.dispatch('admin/createEntry', {});
    });

    // Assign jsTree instance to exported jsTreeInstance object and return it
    jsTreeInstance = $('#tree').jstree(true);

    return jsTreeInstance;
};

let nodeScopes = [];

let validChildModules = [];

const getModuleParents = (moduleId, scopes = []) => {
    const moduleProperties = _.find(store.state.photonModule.modules, { 'id': moduleId });

    scopes.push(moduleProperties);

    validChildModules.push(moduleProperties.table_name);

    if (moduleProperties.category) {
        return getModuleParents(moduleProperties.category, scopes);
    }

    return scopes.reverse();
};

// An object storing all types as key value pairs, where the key is the type name and the value is
// an object that can contain optional keys as documented in
// https://www.jstree.com/api/#/?q=$.jstree.defaults.dnd&f=$.jstree.defaults.types
const buildTypes = () => {
    nodeScopes = getModuleParents(vueComponent.selectedModule.id);

    let types = {};

    _.forEach(nodeScopes, function(moduleProperty){
        const moduleProperties = _.find(store.state.photonModule.modules, { 'table_name': moduleProperty.table_name });

        types[moduleProperty.table_name] = {
            // an object of values which will be used to add HTML attributes on the resulting A DOM node
            // (merged with the node's own data)
            a_attr: moduleProperties.table_name !== vueComponent.selectedModule.table_name
                ? { class: 'text-muted' }
                : false,

            //  string - can be a path to an icon or a className, if using an image that is in
            // the current directory use a ./ prefix, otherwise it will be detected as a class.
            // Omit to use the default icon from your theme.
            icon: moduleProperties.icon,

            // an array of node type strings, that nodes of this type can have as children.
            // Do not specify or set to -1 for no limits.
            valid_children: getValidChildren(moduleProperty),
            // valid_children: [vueComponent.selectedModule.table_name],
            //
            //
        };
    });

    const rootValidChildren = [];

    // If a node is root (parentless) node, and is of sortable or multilevel_sortable type, allow move to root action
    if (!vueComponent.selectedModule.category) {
        rootValidChildren.push(vueComponent.selectedModule.table_name);
    }

    types['#'] = {
        valid_children: rootValidChildren,
    };

    return types;
};

const getValidChildren = (moduleProperty) => {
    // Returns self if the moduleProperty is the module currently being edited, and it's type is multilevel_sortable
    if (moduleProperty.table_name === vueComponent.selectedModule.table_name
        && moduleProperty.type === 'multilevel_sortable') {
        return [moduleProperty.table_name];
    }

    // Returns currently edited module, if moduleProperty is currently edited module's parent
    if(moduleProperty.id === vueComponent.selectedModule.category) {
        return [vueComponent.selectedModule.table_name];
    }

    return[];
};

/**
 * Conducts a new node selection
 *
 * @param   {integer}  moduleEntryId
 * @return  {void}
 */
export const jsTreeReselectNode = (moduleEntryId) => {
    const tableName = vueComponent.selectedModule.table_name;

    if (jsTreeInstance) {
        jsTreeInstance.deselect_all(true);

        jsTreeInstance.select_node(`${tableName}.${moduleEntryId}`, true);
    }
};

/**
 * On jsTree ready handler
 *
 * @return  {void}
 */
export const onJsTreeReady = () => {
    $('#tree').addTouch();

    jsTreeInstance.deselect_all(true);

    const entryId = store.state.admin.editedEntry.id;

    const tableName = vueComponent.selectedModule.table_name;

    vueComponent.jsTreeInitialized = true;

    // In no entry is being edited
    if (!store.state.admin.editedEntry.id) {
        return;
    }

    if (!(vueComponent.selectedModule.type === 'sortable'
        || vueComponent.selectedModule.type === 'multilevel_sortable')) {
        jsTreeInstance.select_node(`${tableName}.${entryId}`, true);

        return;
    }

    const uri = `${apiNodesBasePath}/${tableName}/ancestors/${entryId}`;

    api.get(uri)
        .then((response) => {
            const entries = response.data.body.ancestors;

            const ancestors = entries.map(function(node){
                return `${node.table_name}.${node.id}`;
            }).reverse();

            // Loads a node (fetches its children using the core.data setting).
            // Multiple nodes can be passed to by using an array.
            jsTreeInstance.load_node(ancestors, function(){
                onAllNodesLoaded();
            });
        })
        .catch((response) => {
            if (response.body.message==='MODULE_PARENT_NOT_FOUND') {
                jsTreeInstance.select_node(`${tableName}.${entryId}`, true);

                return;
            }

            pError(`Failed to load ancestors for jsTree node ${tableName}.${entryId}`, response);
        });
};

const onAllNodesLoaded = () => {
    const entryId = store.state.admin.editedEntry.id;

    const tableName = vueComponent.selectedModule.table_name;

    let hasNodes = true;

    if (vueComponent.selectedModule.type !== 'non_sortable' && $('#tree').find('li').length === 0) {
        hasNodes = false;
    }

    store.dispatch('ui/updateHasNodes', { hasNodes });

    $('#tree').off('dblclick', '.jstree-anchor');

    $('#tree').on('dblclick', '.jstree-anchor', nodeDoubleClickHandler);

    $('#tree').off('click', '.jstree-clicked');

    if (store.state.admin.editorMode === 'default' || store.state.admin.editorMode === 'create') {
        $('#tree').on('click', '.jstree-clicked', function () {
            if (store.state.admin.editorMode === 'default' || store.state.admin.editorMode === 'create') {
                $('#tree').jstree(true).deselect_node(this);
            }
        });
    }

    jsTreeInstance.select_node(`${tableName}.${entryId}`, true);

    // lazyLoadNodes();

};

/**
 * Expose jsTree refresh method
 *
 * @return  {void}
 */
export const refreshJsTree = () => {
    jsTreeInstance.refresh();
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
