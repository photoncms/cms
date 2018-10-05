import { router } from '_/router/router';
import { store } from '_/vuex/store';

export default function() {
    const instance = $.jstree.reference(this);

    const node = instance.get_node(this);

    const stringifiedModuleId = String(store.state.generator.selectedModule.id);

    // If module node being selected has already been selected
    if (stringifiedModuleId === node.id) {
        return;
    }

    router.push(`/generator/${node.original.tableName}`);
}
