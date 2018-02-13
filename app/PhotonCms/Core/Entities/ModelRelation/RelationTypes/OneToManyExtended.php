<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes;

use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\MigrationRelationInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\BaseRelation;
use Photon\PhotonCms\Core\Helpers\FileContentHelper;

class OneToManyExtended extends BaseRelation implements ModelRelationInterface, MigrationRelationInterface
{

    protected $fieldType;

    /**
     * Namespace of the target model.
     *
     * @var string
     */
    private $targetModelNamespace;

    /**
     * Name of the source table for relation.
     *
     * @var string
     */
    private $sourceTable;

    /**
     * Name of the source field from the source table.
     *
     * @var string
     */
    private $sourceField;

    /**
     * Name of the target table for relation.
     *
     * @var string
     */
    protected $targetTable;

    /**
     *
     * @param string $relationName
     * @param string $targetModelNamespace
     */
    public function __construct(
        $fieldType,
        $relationName,
        $targetModelNamespace,
        $sourceTable,
        $targetTable,
        $sourceField,
        $targetField = ''
    )
    {
        $this->fieldType            = $fieldType;
        $this->relationName         = $relationName;
        $this->targetModelNamespace = $targetModelNamespace;
        $this->sourceTable          = $sourceTable;
        $this->targetTable          = $targetTable;
        $this->sourceField          = $sourceField == $relationName ? 'id' : $sourceField;
        if ($targetField !== null && $targetField !== '') {
            $this->targetField = $targetField;
        }
    }

    /**
     * Compiles the relation into a string representing the model code for a relation.
     *
     * @return string
     */
    public function compile($amountOfIndent = 0)
    {
        $content = '';
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= "public function {$this->relationName}_relation() {";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "return \$this->hasMany('{$this->targetModelNamespace}'";
        if ($this->targetField) {
            $content .= ", '{$this->targetField}'";
            if ($this->sourceField) {
                $content .= ", '{$this->sourceField}'";
            }
        }

        elseif ($this->sourceField) {
            throw new BaseException('MISSING_TARGET_ATTRIBUTE_NAME');
        }
        $content .= ");";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= '}';
        FileContentHelper::addNewLines($content);

        return $content;
    }

    public function compileMigration($amountOfIndent = 0)
    {
        // There is no migration content for this relation, since the reference field is in another table.
    }

    public function compileDrop($amountOfIndent = 0)
    {
        // Delete the foreign key
        // Delete the source existing field
        // Delete the target existing field
    }
}