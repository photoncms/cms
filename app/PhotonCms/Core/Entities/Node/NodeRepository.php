<?php

namespace Photon\PhotonCms\Core\Entities\Node;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Node\Contracts\RootNodesInterface;
use Photon\PhotonCms\Core\Entities\Node\Contracts\NodeChildrenInterface;
use Illuminate\Database\Eloquent\Model;

use Photon\PhotonCms\Core\Entities\Node\Contracts\MaxDepthInterface;

class NodeRepository
{
    private $supportedActions = [];

    /**
     * Repository constructor.
     *
     * Sets supported actions.
     */
    public function __construct()
    {
        $this->supportedActions = [
            'moveLeft' => function (Node $affectedNode) {
                return $affectedNode->moveLeft();
            },
            'moveRight' => function (Node $affectedNode) {
                return $affectedNode->moveRight();
            },
            'moveToLeftOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestabilityAgainstNode($affectedNode, $targetNode);
                // $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->moveToLeftOf($targetNode);
            },// Move to the node to the left of ...
            'moveToRightOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestabilityAgainstNode($affectedNode, $targetNode);
                // $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->moveToRightOf($targetNode);
            },// Move to the node to the right of ...
            'makeNextSiblingOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestabilityAgainstNode($affectedNode, $targetNode);
                // $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makeNextSiblingOf($targetNode);
            },// Alias for moveToRightOf.
            'makeSiblingOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestabilityAgainstNode($affectedNode, $targetNode);
                // $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makeSiblingOf($targetNode);
            },// Alias for makeNextSiblingOf.
            'makePreviousSiblingOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestabilityAgainstNode($affectedNode, $targetNode);
                // $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makePreviousSiblingOf($targetNode);
            },// Alias for moveToLeftOf.
            'makeChildOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestability($affectedNode);
                $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makeChildOf($targetNode);
            },// Make the node a child of ...
            'makeFirstChildOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestability($affectedNode);
                $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makeFirstChildOf($targetNode);
            },// Make the node the first child of ...
            'makeLastChildOf' => function (Node $affectedNode, Node $targetNode) {
                $this->validateNodeNestability($affectedNode);
                $this->guardNodeMaxDepth($affectedNode, $targetNode);
                return $affectedNode->makeLastChildOf($targetNode);
            },// Alias for makeChildOf.
            'makeRoot' => function (Node $affectedNode) {
                return $affectedNode->makeRoot();
            },// Make current node a root node
            'setScope' => function (Node $affectedNode, Model $target) {
                return $affectedNode->setScope($target);
            },
            'unsetScope' => function (Node $affectedNode) {
                return $affectedNode->unsetScope();
            }
        ];
    }

    /**
     * Finds a node by id.
     *
     * @param int $nodeId
     * @param RootNodesInterface $gateway
     * @return Node
     */
    public function find($nodeId, RootNodesInterface $gateway)
    {
        return $gateway->retrieve($nodeId);
    }

    /**
     * Gets all root nodes for a single model by table name.
     *
     * @param string $modelName
     * @param RootNodesInterface $gateway
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findRootNodesForModel(RootNodesInterface $gateway, $filterData = [])
    {
        $nodes = $gateway->retrieveRootNodes($filterData);

        return $nodes;
    }

    /**
     * Retrieves all child nodes
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $node
     * @param NodeChildrenInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findChildren(Node $node, NodeChildrenInterface $gateway)
    {
        return $gateway->retrieveChildren($node);
    }

    /**
     * Find root nodes by scope id.
     *
     * @param int $scopeId
     * @param NodeChildrenInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findRootNodesByScopeId($scopeId, NodeChildrenInterface $gateway)
    {
        return $gateway->retrieveRootNodesByScopeId($scopeId);
    }

    /**
     * Performs a single Baum Node action on an affected node. If targeted node is reguired, it must be passed.
     *
     * @param Node $affectedNode
     * @param string $action
     * @param Mixed $targetNode
     * @return Mixed
     * @throws \Exception
     */
    public function performNodeAction($affectedNode, $action, $targetNode = null)
    {
        if (!isset($this->supportedActions[$action])) {
            throw new PhotonException('INVALID_NODE_ACTION', ['action' => $action]);
        }

        $function = $this->supportedActions[$action];

        if ($targetNode !== null) {
            $affectedNode->prepareCloneBeforeReposition();
            $affectedNode->firePreNodeReposition();
            $response = $function($affectedNode, $targetNode);
            $affectedNode->prepareCloneAfterReposition();
            $affectedNode->firePostNodeReposition();
            return $response;
        }
        else {
            return $function($affectedNode);
        }
    }

    /**
     * If a node reaches the maximum depth allowed by the menu exception will be thrown.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $affectedNode
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $targetNode
     * @throws PhotonException
     */
    public function guardNodeMaxDepth(Node $affectedNode, Node $targetNode)
    {
        if ($affectedNode instanceof MaxDepthInterface) {
            $maxDepth = $affectedNode->getMaxDepth();
            $nextLevel = $targetNode->depth + 1;

            if ($maxDepth !== null) {
                // Checking the affected node depth
                if ($nextLevel > $maxDepth) {
                    throw new PhotonException('NODE_MAX_DEPTH_REACHED', ['max' => $maxDepth, 'requested_depth' => $nextLevel]);
                }

                // Checking the children depth
                $depthDiff = 0;
                $descendants = $affectedNode->getDescendants();
                foreach ($descendants as $descendant) {
                    $tmpDepthDiff = $descendant->depth - $affectedNode->depth;
                    if($tmpDepthDiff > $depthDiff)
                        $depthDiff = $tmpDepthDiff;
                }

                if($depthDiff + $nextLevel > $maxDepth) {
                    throw new PhotonException('NODE_MAX_DESCENDANT_DEPTH_REACHED', ['max' => $maxDepth, 'requested_depth' => $depthDiff + $nextLevel]);
                }
            }
        }
    }

    /**
     * Validates the ability to nest a node.
     * Throws an exception if unable.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $node
     * @throws PhotonException
     */
    private function validateNodeNestability(Node $node)
    {
        if ($node instanceof NonNestableScopedNode) {
            throw new PhotonException('NODE_NESTING_NOT_ALLOWED');
        }
    }

    /**
     * Validates the ability to nest a node within a node.
     * Throws an exception if unable.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $affectedNode
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $targetNode
     * @throws PhotonException
     */
    private function validateNodeNestabilityAgainstNode(Node $affectedNode, Node $targetNode)
    {
        if ($affectedNode instanceof NonNestableScopedNode && $targetNode->getLevel() > 0) {
            throw new PhotonException('NODE_NESTING_NOT_ALLOWED');
        }
    }
}