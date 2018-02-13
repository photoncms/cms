import { router } from '_/router/router';

import { store } from '_/vuex/store';

/**
 * Handles the node doubleclick event
 *
 * @return  {void}
 */
export default function() {
    const instance = $.jstree.reference(this);

    const node = instance.get_node(this);

    const entryId = store.state.menuItemsEditor.editedEntry.id;

    // Do not reload if currently edited node is doubleclicked
    if (node.original.id === entryId) {
        return false;
    }

    const menuId = router.currentRoute.params.menuId;

    router.push(`/menu-items-editor/${menuId}/${node.original.id}`);
}
