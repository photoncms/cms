<?php

namespace Photon\PhotonCms\Core\Controllers;

// General
use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;

// Dependency injection
use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Node\NodeLibrary;
use Photon\PhotonCms\Core\Entities\Node\NodeRepository;
use Photon\PhotonCms\Core\Entities\Node\NodeTransformer;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Response\ResponseRepository;

class NodeController extends Controller
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var ModuleGatewayInterface
     */
    private $moduleGateway;

    /**
     * @var NodeLibrary
     */
    private $nodeLibrary;

    /**
     * @var DynamicModuleLibrary
     */
    private $dynamicModuleLibrary;

    /**
     * @var ModuleLibrary
     */
    private $moduleLibrary;

    /**
     * @var \Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleInterrupter
     */
    private $interrupter;

    /**
     * @var NodeTransformer
     */
    private $nodeTransformer;

    /**
     * Controller constructor.
     *
     * @param ResponseRepository $responseRepository
     * @param ModuleRepository $moduleRepository
     * @param NodeRepository $nodeRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param NodeLibrary $nodeLibrary
     * @param DynamicModuleLibrary $dynamicModuleLibrary
     * @param ModuleLibrary $moduleLibrary
     * @param NodeTransformer $nodeTransformer
     */
    public function __construct(
        ResponseRepository $responseRepository,
        ModuleRepository $moduleRepository,
        NodeRepository $nodeRepository,
        ModuleGatewayInterface $moduleGateway,
        NodeLibrary $nodeLibrary,
        DynamicModuleLibrary $dynamicModuleLibrary,
        ModuleLibrary $moduleLibrary,
        NodeTransformer $nodeTransformer
    ) {
        $this->responseRepository = $responseRepository;
        $this->moduleRepository = $moduleRepository;
        $this->nodeRepository = $nodeRepository;
        $this->moduleGateway = $moduleGateway;
        $this->nodeLibrary = $nodeLibrary;
        $this->dynamicModuleLibrary = $dynamicModuleLibrary;
        $this->moduleLibrary = $moduleLibrary;
        $this->interrupter = \App::make('\Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleInterrupter');
        $this->nodeTransformer = $nodeTransformer;
    }

    /**
     * Retrieves a node with its ancestors.
     * Landing method for HTTP request.
     *
     * First the node module and its scope parent modules are retrieved.
     * Then the node is rertieved with children and scoped children.
     * Then recursively for each node the proces is repeated.
     *
     * @param string $tableName
     * @param int $nodeId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getNodeAncestors($tableName, $nodeId)
    {
        $nodes = $this->getDynamicModuleNodeAncestors($tableName, $nodeId);

        return $this->responseRepository->make('LOAD_NODE_ANCESTORS_SUCCESS', ['ancestors' => $nodes]);
    }

    /**
     * Retrieves a node with its ancestors.
     *
     * First the node module and its scope parent modules are retrieved.
     * Then the node is rertieved with children and scoped children.
     * Then recursively for each node the proces is repeated.
     *
     * @param string $tableName
     * @param int $nodeId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getDynamicModuleNodeAncestors($tableName, $nodeId)
    {
        $module = $this->moduleLibrary->findByTableName($tableName);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $node = $this->nodeLibrary->findNodeByTableNameAndId($tableName, $nodeId);
        if (is_null($node)) {
            throw new PhotonException('NODE_NOT_FOUND', ['node_id' => $nodeId]);
        }

        $nodeAncestralTree = $this->nodeLibrary->getNodeAncestors($node);

        $transformedAncestralTree = [];
        foreach ($nodeAncestralTree as $ancestor) {
            $transformedAncestralTree[] = $this->nodeTransformer->transformForJSTreeAncestor($ancestor);
        }

        return $transformedAncestralTree;
    }

    /**
     * Gets a node with its children, or gets root nodes if the node ID isn't provided.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @param int $nodeId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getNodeChildren($tableName, $nodeId = null)
    {
        $childModules = \Input::get('child_modules', null);

        $children = $this->getDynamicModuleNodeChildren($tableName, $nodeId, $childModules);

        return $this->responseRepository->make('LOAD_NODE_SUCCESS', ['nodes' => $children]);
    }

    /**
     * Gets a node with its children, or gets root nodes if the node ID isn't provided.
     *
     * @param string $tableName
     * @param int $nodeId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getDynamicModuleNodeChildren($tableName, $nodeId = null, $childModules = null)
    {
        $module = $this->moduleLibrary->findByTableName($tableName);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $allChildren = [];
        if ($nodeId) {
            $node = $this->nodeLibrary->findNodeByTableNameAndId($tableName, $nodeId);
            if (is_null($node)) {
                throw new PhotonException('NODE_NOT_FOUND', ['node_id' => $nodeId]);
            }

            $children = $this->nodeLibrary->getNodeChildren($node);
            $scopedChildren = $this->nodeLibrary->getScopedChildren($tableName, $node->id, $childModules);
            $allChildren = array_merge($children, $scopedChildren);
        } else {
            $allChildren = $this->nodeLibrary->findRootNodesByTableName($tableName);
        }

        $transformedChildren = [];
        foreach ($allChildren as $child) {
            $transformedChild = $this->nodeTransformer->transformForJSTreeNode($child);
            $nodeChildren = $this->nodeLibrary->getNodeChildren($child);
            $scopedChildren = $this->nodeLibrary->getScopedChildren($child->getTable(), $child->id, $childModules);
            $transformedChild['has_children'] = !(empty($nodeChildren) && empty($scopedChildren));
            $transformedChildren[] = $transformedChild;
        }

        return $transformedChildren;
    }

    /**
     * Repositions nodes by calling the requested action over node repository.
     * Landing method for HTTP request.
     *
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function repositionNode()
    {
        $action = \Request::get('action');
        $affected = \Request::get('affected');
        $target = \Request::get('target');

        $affectedNode = $this->repositionDynamicModuleNode($action, $affected, $target);

        return $this->responseRepository->make('REPOSITION_NODE_SUCCESS', ['affected_node' => $affectedNode]);
    }

    /**
     * Repositions nodes by calling the requested action over node repository.
     *
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function repositionDynamicModuleNode($action, $affected, $target)
    {
        // Prepare affected node
        $affectedNode = $this->dynamicModuleLibrary->findEntryByTableNameAndId($affected['table'], $affected['id']);

        if (!$affectedNode) {
            throw new PhotonException('AFFECTED_NODE_NOT_FOUND', ['id' => $affected['id']]);
        }

        // Second node placeholder
        $secondNode = null;

        // Prepare scope node
        if ($target && isset($target['id']) && $action === 'setScope') {
            $scopeNode = $this->dynamicModuleLibrary->getParentScopeItemByTableNameAndId($affected['table'], $target['id']);

            if (!$scopeNode) {
                throw new PhotonException('SCOPE_NODE_NOT_FOUND', ['id' => $target['id']]);
            }

            $secondNode = $scopeNode;
        }

        // Prepare targeted node
        if (isset($affected['table']) && isset($target['id']) && $action != 'setScope') {
            $targetNode = $this->dynamicModuleLibrary->findEntryByTableNameAndId($affected['table'], $target['id']);

            if (!$targetNode) {
                throw new PhotonException('TARGET_NODE_NOT_FOUND', ['id' => $target['id']]);
            }

            $secondNode = $targetNode;
        }

        $affectedNode = $this->nodeRepository->performNodeAction($affectedNode, $action, $secondNode);

        return $affectedNode;
    }
}
