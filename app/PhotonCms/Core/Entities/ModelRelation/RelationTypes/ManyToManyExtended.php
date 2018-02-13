<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes;

use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\MigrationRelationInterface;
use Photon\PhotonCms\Core\Helpers\RelationsHelper;
use Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\BaseRelation;
use Photon\PhotonCms\Core\Helpers\FileContentHelper;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationCanBeDropped;

class ManyToManyExtended extends BaseRelation implements ModelRelationInterface, MigrationRelationInterface, ModelRelationCanBeDropped
{

    protected $fieldType;

    /**
     * Target model namespace.
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
     * Name of the target table for relation.
     *
     * @var string
     */
    protected $targetTable;

    /**
     * Name of the source field from the source table.
     *
     * @var string
     */
    private $sourceField;

    /**
     * Name of the pivot table.
     *
     * @var string
     */
    public $pivotTable;

    public $sourcePivotField;

    private $targetPivotField;

    protected $requiresPivot = true;

    /**
     * Many to many class contructor.
     *
     * @param string $relationName
     * @param string $targetModelNamespace
     * @param string $sourceTable
     * @param string $targetTable
     * @param string $pivotTable
     * @param string $sourceField
     * @param string $targetField
     */
    public function __construct(
        $fieldType,
        $relationName,
        $targetModelNamespace,
        $sourceTable,
        $targetTable,
        $sourceField,
        $targetField = '',
        $pivotTable = ''
    )
    {
        $this->fieldType            = $fieldType;
        $this->relationName         = $relationName;
        $this->targetModelNamespace = $targetModelNamespace;
        $this->sourceTable          = $sourceTable;
        $this->targetTable          = $targetTable;
        $this->sourceField          = $sourceField;
        if ($targetField !== null && $targetField !== '') {
            $this->targetField = $targetField;
        }
        $this->pivotTable = (!$pivotTable || $pivotTable === '')
            ? RelationsHelper::generatePivotTableName($sourceTable, $targetTable)
            : $pivotTable;

        $this->sourcePivotField = RelationsHelper::generateFieldNameFromTableName($this->sourceTable);
        $this->targetPivotField = RelationsHelper::generateFieldNameFromTableName($this->targetTable);
        if($this->sourcePivotField == $this->targetPivotField)
            $this->targetPivotField = 'alt_'.$this->targetPivotField;
    }

    /**
     * Compiles a relation method.
     *
     * @return string
     */
    public function compile($amountOfIndent = 0)
    {
        $content = '';
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= "public function {$this->relationName}_relation() {";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "return \$this->belongsToMany('{$this->targetModelNamespace}', '{$this->pivotTable}', '{$this->sourcePivotField}', '{$this->targetPivotField}');";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= '}';
        FileContentHelper::addNewLines($content);

        return $content;
    }

    public function compileMigration($amountOfIndent = 0)
    {
    }

    public function compilePivotMigration($amountOfIndent = 0)
    {
        $content = '';
        FileContentHelper::addLinesAndIndent($content, 2, $amountOfIndent);
        $content .= "if (!Schema::hasTable('{$this->pivotTable}')) {";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "Schema::create('{$this->pivotTable}', function (Blueprint \$table) {";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+2);
        $content .= "\$table->integer('{$this->sourcePivotField}')->unsigned()->index();";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+2);
        $content .= "\$table->integer('{$this->targetPivotField}')->unsigned()->index();";
        FileContentHelper::addLinesAndIndent($content, 2, $amountOfIndent+2);
        $content .= "\$table->foreign('{$this->targetPivotField}')->references('id')->on('{$this->targetTable}')->onDelete('cascade');";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+2);
        $content .= "\$table->foreign('{$this->sourcePivotField}')->references('id')->on('{$this->sourceTable}')->onDelete('cascade');";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "});";
        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= "}";

        return $content;
    }

    public function compileDrop($amountOfIndent = 0)
    {
        $content = '';

        FileContentHelper::addIndent($content, $amountOfIndent);
        $content .= "Schema::table('{$this->sourceTable}', function (Blueprint \$table) {";

        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent+1);
        $content .= "\Schema::dropIfExists('{$this->pivotTable}');";


        FileContentHelper::addLinesAndIndent($content, 1, $amountOfIndent);
        $content .= '});';

        return $content;
    }
}