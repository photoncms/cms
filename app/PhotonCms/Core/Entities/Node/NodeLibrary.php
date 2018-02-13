<?php

namespace Photon\PhotonCms\Core\Entities\Node;

use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Node\NodeRepository;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplateFactory;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGatewayFactory;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\Node\Node;

class NodeLibrary
{

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
     * Controller constructor.
     *
     * @param ModuleRepository $moduleRepository
     * @param NodeRepository $nodeRepository
     * @param ModuleGatewayInterface $moduleGateway
     */
    public function __construct(
        ModuleRepository $moduleRepository,
        NodeRepository $nodeRepository,
        ModuleGatewayInterface $moduleGateway
    )
    {
        $this->moduleRepository     = $moduleRepository;
        $this->nodeRepository       = $nodeRepository;
        $this->moduleGateway        = $moduleGateway;
    }

    /**
     * Finds node by table name and ID.
     *
     * @param string $tableName
     * @param int $id
     * @return Node
     * @throws PhotonException
     */
    public function findNodeByTableNameAndId($tableName, $id)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);

        return $this->nodeRepository->find($id, $dynamicModuleGateway);
    }

    /**
     * Finds node by table name and ID.
     *
     * @param string $tableName
     * @param int $id
     * @return Node
     * @throws PhotonException
     */
    public function findRootNodesByTableName($tableName)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);

        return $this->nodeRepository->findRootNodesForModel($dynamicModuleGateway);
    }

    /**
     * Retrieves Node children
     *
     * @param string $tableName
     * @param Node $node
     * @return array
     * @throws PhotonException
     */
    public function getNodeChildren(Node $node)
    {
        $module = $this->moduleRepository->findModuleByTableName($node->getTable(), $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $node->getTable()]);
        }

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);

        return $this->nodeRepository->findChildren($node, $dynamicModuleGateway)->all();
    }

    /**
     * Retrieves scoped children.
     *
     * @param string $tableName
     * @param int $nodeId
     * @return array
     * @throws PhotonException
     */
    public function getScopedChildren($tableName, $nodeId, $childModules = null)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $scopedModules = $this->moduleRepository->findScopedModulesByParentId($module->id, $this->moduleGateway);

        $children = [];
        foreach ($scopedModules as $scopedModule) {
            if (is_array($childModules) && !in_array($scopedModule->table_name, $childModules)) {
                continue;
            }
            $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
            $modelTemplate->setModelName($scopedModule->model_name);

            $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);

            $rootNodes = $this->nodeRepository->findRootNodesByScopeId($nodeId, $dynamicModuleGateway);

            $resultArray = $rootNodes->all();

            $children = array_merge($children, $resultArray);
        }

        return $children;
    }

    /**
     * Gets scoped parent node.
     *
     * @param Node $tableName
     * @param int $nodeScopeId
     * @return Node
     * @throws PhotonException
     */
    public function getNodeScopeParentNode(Node $node, $nodeScopeId)
    {
        $module = $this->moduleRepository->findModuleByTableName($node->getTable(), $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $node->getTable()]);
        }

        $scopeParentModule = $this->moduleRepository->findById($module->category, $this->moduleGateway);

        if (is_null($scopeParentModule)) {
            throw new PhotonException('MODULE_PARENT_NOT_FOUND', ['id' => $module->category]);
        }

        $scopeParentNode = $this->findNodeByTableNameAndId($scopeParentModule->table_name, $node->scope_id);

        if (!$scopeParentNode) {
            throw new PhotonException('SCOPE_PARENT_NODE_NOT_FOUND', ['table_name' => $node->getTable(), 'id' => $node->scope_id]);
        }

        return $scopeParentNode;
    }

    /**
     * Retrieves all ancestors (direct and scoped) for the supplied node.
     *
     * @param Node $node
     * @param array $nodeAncestralTree
     * @return array
     */
    public function getNodeAncestors(Node $node, &$nodeAncestralTree = [])
    {
        $nodeParent = $node->parent()->get();
        if ($nodeParent->isEmpty()) {
            $nodeParent = $this->getNodeScopeParentNode($node, $node->id);
        }
        else {
            $nodeParent = $nodeParent->first();
        }

        if ($nodeParent) {
            $nodeAncestralTree[] = $nodeParent;
            try {
                $this->getNodeAncestors($nodeParent, $nodeAncestralTree);
            } catch (PhotonException $ex) {
                // we'll just ignore this cause the only reason for this to fail is if we've reached the end of the ancestral tree
            }
        }

        return $nodeAncestralTree;
    }

    /**
     * Checks if a node has any nested or scoped children.
     *
     * @param Node $node
     * @return boolean
     */
    public function nodeHasChildren(Node $node)
    {
        $nodeChildren = $this->getNodeChildren($node);
        $scopedChildren = $this->getScopedChildren($node->getTable(), $node->id);
        return !(empty($nodeChildren) && empty($scopedChildren));
}
}