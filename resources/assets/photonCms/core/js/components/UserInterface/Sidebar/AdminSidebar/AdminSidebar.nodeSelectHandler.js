import { store } from '_/vuex/store';

export default function (event, action) {
    // Added to ignore function calls not triggered by user action (no event or node objects passed)
    if (!action.node || !action.event) {
        return;
    }

    if(!(store.state.admin.editorMode === 'create' || store.state.admin.editorMode === 'default')) {
        return;
    }

    let parentId = null;

    let scopeId = null;

    // If a selected node belongs to a parent module
    if (action.node.original.tableName !== store.state.admin.selectedModule.table_name) {
        scopeId = action.node.original.originalId;
    }

    if (action.node.original.tableName === store.state.admin.selectedModule.table_name) {
        parentId = action.node.original.originalId;

        scopeId =  action.node.original.scopeId;
    }

    store.dispatch('admin/updateSelectedNode', {
        anchorText: action.node.original.text,
        url: `${action.node.original.tableName}/${action.node.original.originalId}`,
        parentId,
        scopeId,
    });
}
