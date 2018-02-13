import { jsTreeInstance } from '_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.jsTree';
import { api } from '_/services/api';
import { config } from '_/config/config';
import { pLog, pError } from '_/helpers/logger';

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

    if (previousNodeDom
            && previousNodeDom.length > 0
            && previousNode.original.tableName === moveData.node.original.tableName) {
        if(previousNode.original.scopeId !== moveData.node.original.scopeId) {
            // If ScopeId is different we need to provide the scopeId parameter
            moveNode('moveToRightOf', moveData.node.id, previousNodeDom[0].id, previousNode.original.scopeId);

            return;
        }

        moveNode('moveToRightOf', moveData.node.id, previousNodeDom[0].id);

        return;
    }

    if (nextNodeDom
            && nextNodeDom.length > 0
            && nextNode.original.tableName === moveData.node.original.tableName) {
        if(nextNode.original.scopeId !== moveData.node.original.scopeId) {
            // If ScopeId is different we need to provide the scopeId parameter
            moveNode('moveToLeftOf', moveData.node.id, nextNodeDom[0].id, nextNode.original.scopeId);

            return;
        }

        moveNode('moveToLeftOf', moveData.node.id, nextNodeDom[0].id);

        return;
    }

    if (moveData.old_parent !== moveData.parent) {
        const newParent = jsTreeInstance.get_node(moveData.parent);

        if (moveData.node.original.tableName === newParent.original.tableName) {
            if(moveData.node.original.scopeId !== newParent.original.scopeId) {
                // If ScopeId is different we need to provide the scopeId parameter
                moveNode('makeChildOf', moveData.node.id, moveData.parent, newParent.original.scopeId);

                return;
            }

            moveNode('makeChildOf', moveData.node.id, moveData.parent);

            return;
        }

        const targetNode = splitNodeId(moveData.parent);

        moveNode('setScope', moveData.node.id, moveData.parent, targetNode.id);

        return;
    }

    pError(`Error initiating moving of node ${moveData.node.id}`);
}

const splitNodeId = (nodeId) => {
    const nodeData = nodeId.split('.');

    return {
        id: nodeData[1],
        table_name: nodeData[0],
    };
};

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

    const affectedNode = splitNodeId(affectedNodeId);

    const targetNode = targetScope ? { id: targetScope } : splitNodeId(targetNodeId);

    const uri = `${config.ENV.apiBasePath}/nodes/reposition`;

    const payload = {
        action: chosenAction,
        affected: {
            id: affectedNode.id,
            table: affectedNode.table_name,
        },
        target: {
            id: targetNode.id
        }
    };

    api.put(uri, payload)
        .then((response) => {
            // If targetScope is passed we need to set the new scope first, and on success reposition the node.
            if (targetScope) {
                pLog(`Sucessfuly changed scope of node ${affectedNode.table_name}.${affectedNode.id}`, response);

                // Recurse only if action is different than setScope
                if (action !== 'setScope') {
                    moveNode(action, affectedNodeId, targetNodeId);
                }
            }

            pLog(`Sucessfuly moved node ${affectedNode.table_name}.${affectedNode.id}`, response);

            jsTreeInstance.refresh();
        })
        .catch((response) => {
            pError(`Error moving node ${affectedNode.table_name}.${affectedNode.id}`, response);

            jsTreeInstance.refresh();
        });
};
