import { jsTreeInstance } from '_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.jsTree';

import { store } from '_/vuex/store';

import { router } from '_/router/router';

export default (event, action) => {
    if (!action.node || !action.event) {
        return;
    }

    const stringifiedModuleId = String(store.state.generator.selectedModule.id);

    // If module node being selected has already been selected
    if (stringifiedModuleId === action.node.id) {
        // Deselects the node clicked
        jsTreeInstance.deselect_node(action.node.id, true);

        // Reroute to root generator view
        router.push('/generator');

        return;
    }

    // Else if node being selected has not already been selected reroute to that selected module generator view
    router.push(`/generator/${action.node.original.tableName}`);
};
