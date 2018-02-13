import { store } from '_/vuex/store';

/**
 * Handles the node selection event
 *
 * @return  {void}
 */
export default function (event, action) {
    // Added to ignore function calls not triggered by user action (no event or node objects passed)
    if (!action.node || !action.event) {
        return;
    }

    if(!(store.state.menuItemsEditor.editorMode === 'create' || store.state.menuItemsEditor.editorMode === 'default')) {
        return;
    }

    return;
}
