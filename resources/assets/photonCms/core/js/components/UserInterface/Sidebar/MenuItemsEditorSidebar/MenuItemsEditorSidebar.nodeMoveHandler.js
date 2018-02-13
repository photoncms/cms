import { jsTreeInstance } from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.jsTree';

import { api } from '_/services/api';

import { config } from '_/config/config';

import { pLog, pError } from '_/helpers/logger';

import { router } from '_/router/router';

/**
 * Node move handler
 *
 * @param   {event}  event
 * @param   {object}  moveData
 * @return  {void}
 */
export default function (event, moveData) {
    const previousNodeDom = jsTreeInstance.get_prev_dom(moveData.node, true);

    const nextNodeDom = jsTreeInstance.get_next_dom(moveData.node, true);

    const previousNode = previousNodeDom ? jsTreeInstance.get_node(previousNodeDom[0].id) : null;

    const nextNode = nextNodeDom ? jsTreeInstance.get_node(nextNodeDom[0].id) : null;

    if (previousNodeDom && previousNodeDom.length > 0) {
        if(previousNode.original.parentId !== moveData.node.original.parentId) {
            // If parentId is different we need to provide the parentId parameter
            moveNode('moveToRightOf', moveData.node.id, previousNodeDom[0].id, previousNode.original.parentId);

            return;
        }

        moveNode('moveToRightOf', moveData.node.id, previousNodeDom[0].id);

        return;
    }

    if (nextNodeDom && nextNodeDom.length > 0) {
        if(nextNode.original.parentId !== moveData.node.original.parentId) {
            // If parentId is different we need to provide the parentId parameter
            moveNode('moveToLeftOf', moveData.node.id, nextNodeDom[0].id, nextNode.original.parentId);

            return;
        }

        moveNode('moveToLeftOf', moveData.node.id, nextNodeDom[0].id);

        return;
    }

    if (moveData.old_parent !== moveData.parent) {
        const newParent = jsTreeInstance.get_node(moveData.parent);

        if(moveData.node.original.parentId !== newParent.original.parentId) {
            // If parentId is different we need to provide the parentId parameter
            moveNode('makeChildOf', moveData.node.id, moveData.parent, newParent.original.parentId);

            return;
        }

        moveNode('makeChildOf', moveData.node.id, moveData.parent);
    }

    pError(`Error initiating moving of node ${moveData.node.id}`);
}

/**
 * Move Node method. Also handles a use case of setting a new node scope if targetScope parameter is passed. If that
 * is the case moveNode will recurse to perform the original action upon successful setScope action.
 * If the setScope is provided as action parameter, the function will not recurse.
 *
 * @param   {string}  action
 * @param   {string}  affectedNodeId
 * @param   {string}  targetNodeId
 * @param   {int}  targetScope
 * @return  {void}
 */
const moveNode = (action, affectedNodeId, targetNodeId, targetScope = null) => {
    const chosenAction = targetScope ? 'setScope' : action;

    const targetNode = targetScope ? { id: targetScope } : targetNodeId;

    const uri = `${config.ENV.apiBasePath}/menus/items/reposition`;

    const payload = {
        menu_id: router.currentRoute.params.menuId,
        action: chosenAction,
        affected_item_id: affectedNodeId,
        target_item_id: targetNode,
    };

    api.put(uri, payload)
        .then((response) => {
            // If targetScope is passed we need to set the new scope first, and on success reposition the node.
            if (targetScope) {
                pLog(`Sucessfuly changed scope of node ${affectedNodeId}`, response);

                // Recurse only if action is different than setScope
                if (action !== 'setScope') {
                    moveNode(action, affectedNodeId, targetNodeId);
                }
            }

            pLog(`Sucessfuly moved node ${affectedNodeId}`, response);

            jsTreeInstance.refresh();
        })
        .catch((response) => {
            pError(`Error moving node ${affectedNodeId}`, response);

            jsTreeInstance.refresh();
        });
};
