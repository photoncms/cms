import { store } from '_/vuex/store';

export default function (event, action) {
    // Added to ignore function calls not triggered by user action (no event or node objects passed)
    if (!action.node || !action.event) {
        return;
    }

    if(!(store.state.admin.editorMode === 'create' || store.state.admin.editorMode === 'default')) {
        return;
    }

    return;
}
