<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes;

use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\MigrationRelationInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\BaseRelation;
use Photon\PhotonCms\Core\Helpers\FileContentHelper;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationCanBeNullableInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationCanBeDropped;

class OneToOne extends BaseRelation implements ModelRelationInterface, MigrationRelationInterface, ModelRelationCanBeNullableInterface, ModelRelationCanBeDropped
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

    private $nullable = false;

    protected $hasAttribute = true;

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
        $targetField = '',
        $disregardPivot = null,
        $nullable = false
    )
    {
        $this->fieldType            = $fieldType;
        $this->relationName         = $relationName;
        $this->targetModelNamespace = $targetModelNamespace;
        $this->sourceTable          = $sourceTable;
        $this->targetTable          = $targetTable;
        $this->sourceField          = $sourceField;
        $this->targetField = ($targetField !== null && $targetField !== '')
            ? $targetField
            : 'id';
        $this->nullable = $nullable;
    }

    public function isNullable()
    {
        return (bool) $this->nullable;
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
        $content .= "return \$this->hasOne('{$this->targetModelNamespace}'";
        $content .= ", '{$this->targetField}'";
        $content .= ", '{$this->sourceField}'";
        $content .= ");";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= '}';
        FileContentHelper::addNewLines($content);

        return $content;
    }

    public function compileMigration($amountOfIndent = 0)
    {
        $content = '';
        FileContentHelper::addIndent($content, $amountOfIndent);
        $content .= "\$table->integer('{$this->sourceField}')->unsigned()";
        if ($this->isNullable()) {
            $content .= "->nullable()";
        }
        $content .= ";";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= "\$table->foreign('{$this->sourceField}')->references('{$this->targetField}')->on('{$this->targetTable}')->onDelete('cascade');";
        return $content;
    }

    public function compileDrop($amountOfIndent = 0)
    {
        $content = '';

        FileContentHelper::addIndent($content, $amountOfIndent);
        $content .= "Schema::table('{$this->sourceTable}', function (Blueprint \$table) {";

        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "\$table->dropForeign('{$this->sourceTable}_{$this->sourceField}_foreign');";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "\$table->dropColumn('{$this->sourceField}');";

        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= '});';

        return $content;
    }
}