<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationCompilerInterface;
use Photon\PhotonCms\Core\Helpers\FileContentHelper;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationTemplateInterface;
use Photon\PhotonCms\Core\Entities\ModelRelation\Contracts\ModelRelationCanBeDropped;
use Photon\PhotonCms\Core\Entities\Field\Migrations\FieldUpdateTemplate;

class MigrationCompiler implements MigrationCompilerInterface
{
    /**
     * Compiles the content of a model migration file.
     *
     * @param \PoK\Core\Entities\ModelMigration\ModelMigrationTemplate $modelMigrationTemplate
     * @return string
     */
    public function compile(MigrationTemplateInterface $modelMigrationTemplate)
    {
        // Starting the file
        $content = '<?php';
        FileContentHelper::addNewLines($content, 2);

        // Use
        if ($modelMigrationTemplate->hasUses()) {
            $usedNamespaces = $modelMigrationTemplate->getUses();
            foreach ($usedNamespaces as $usedNamespace) {
                $content .= "use {$usedNamespace};";
                FileContentHelper::addNewLines($content);
            }
        }

        // Starting the class
        FileContentHelper::addNewLines($content);
        $content .= 'class '.$modelMigrationTemplate->getClassName();

        // Inheritance
        if ($modelMigrationTemplate->hasInheritance()) {
            $content .= ' extends '.$modelMigrationTemplate->getInheritance();
        }

        // Implementations
        if ($modelMigrationTemplate->hasImplementations()) {
            $content .= ' implements '.implode(', ', $modelMigrationTemplate->getImplementations());
        }

        // Start of the class content
        FileContentHelper::addNewLines($content);
        $content .= '{';

        // Traits
        if ($modelMigrationTemplate->hasTraits()) {
            FileContentHelper::addLinesAndIndent($content);
            $content .= 'use '.implode(', ', $modelMigrationTemplate->getTraits()).';';
            FileContentHelper::addNewLines($content);
        }

        // Starting the Up function
        FileContentHelper::addLinesAndIndent($content);
        $content .= 'public function up()';
        FileContentHelper::addLinesAndIndent($content);
        $content .= '{';

        // Custom migration code for table generation
        $tablesForCreation = $modelMigrationTemplate->getTablesForCreation();
        if (!empty($tablesForCreation)) {
            foreach ($tablesForCreation as $tableName) {
                $tablePivotRelations = [];

                // Creating the main table
                FileContentHelper::addLinesAndIndent($content, 1, 2);
                $content .= "Schema::create('{$tableName}', function (Blueprint \$table) {";

                // Add field column generation
                // working with instances of \Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttribute here.
                $fields = $modelMigrationTemplate->getTableForCreationFields($tableName);
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        FileContentHelper::addLinesAndIndent($content, 1, 3);
                        $content .= "\$table->{$field->getLaravelType()}(";
                        if ($field->getName() !== 'system') {
                            $content .= "'{$field->getName()}'";
                        }
                        if ($field->hasParameters()) {
                            $content .= ", ".implode(", ", $field->getParameters());
                        }
                        $content .= ")";
                        if ($field->isNullable()) {
                            $content .= "->nullable()";
                        }
                        if ($field->isUnique()) {
                            $content .= "->unique()";
                        }
                        if ($field->hasDefault()) {
                            if(is_null($field->getDefault()))
                                $content .= "->default(null)";
                            else 
                                $content .= "->default('{$field->getDefault()}')";
                        }
                        if ($field->isIndexed()) {
                            $content .= "->index()";
                        }
                        
                        $content .= ";";

                    }
                }

                $tableRelations = $modelMigrationTemplate->getTableForCreationRelations($tableName);
                if (!empty($tableRelations)) {
                    foreach ($tableRelations as $relation) {
                        FileContentHelper::addNewLines($content);
                        $content .= $relation->compileMigration(3);

                        if ($relation->requiresPivot()) {
                            $tablePivotRelations[] = $relation;
                        }
                    }
                }

                FileContentHelper::addIndent($content, 2);
                $content .= '});';

                foreach ($tablePivotRelations as $relation) {
                    $content .= $relation->compilePivotMigration(2);
                }
            }
        }

        // Custom migration code for table update
        $tablesForUpdate = $modelMigrationTemplate->getTablesForUpdate();
        $tablesForRemoval = $modelMigrationTemplate->getTablesForRemoval();
        $tablePivotRelations  = [];
        if (!empty($tablesForUpdate)) {
            foreach ($tablesForUpdate as $tableForUpdate) {
                $fieldsForCreation    = $modelMigrationTemplate->getTableForUpdateFieldsForCreation($tableForUpdate);
                $fieldsForUpdate      = $modelMigrationTemplate->getTableForUpdateFieldsForUpdate($tableForUpdate);
                $fieldsForRemoval     = $modelMigrationTemplate->getTableForUpdateFieldsForRemoval($tableForUpdate);
                $relationsForCreation = $modelMigrationTemplate->getTableForUpdateRelationsForCreation($tableForUpdate);
                $relationsForRemoval  = $modelMigrationTemplate->getTableForUpdateRelationsForRemoval($tableForUpdate);

                // Add relation removal
                if (!empty($relationsForRemoval)) {
                    foreach ($relationsForRemoval as $relation) {
                        if ($relation instanceof ModelRelationCanBeDropped) {
                            FileContentHelper::addNewLines($content, 1);
                            $content .= $relation->compileDrop(2);
                        }
                    }
                }

                // Table update
                FileContentHelper::addLinesAndIndent($content, 1, 2);
                $content .= "Schema::table('{$tableForUpdate}', function (Blueprint \$table) {";

                // Add column generation
                if (!empty($fieldsForCreation)) {
                    foreach ($fieldsForCreation as $field) {
                        FileContentHelper::addLinesAndIndent($content, 1, 3);
                        $content .= "\$table->{$field->getLaravelType()}(";
                        if ($field->getName() !== 'system') {
                            $content .= "'{$field->getName()}'";
                        }
                        if ($field->hasParameters()) {
                            $content .= ", ".implode(", ", $field->getParameters());
                        }
                        $content .= ")";
                        if ($field->isNullable()) {
                            $content .= "->nullable()";
                        }
                        if ($field->isUnique()) {
                            $content .= "->unique()";
                        }
                        if ($field->hasDefault()) {
                            if(is_null($field->getDefault()))
                                $content .= "->default(null)";
                            else 
                                $content .= "->default('{$field->getDefault()}')";
                        }
                        $content .= ";";
                    }
                }

                // Add relation creation
                if (!empty($relationsForCreation)) {
                    foreach ($relationsForCreation as $relation) {
                        FileContentHelper::addNewLines($content);
                        $content .= $relation->compileMigration(3);

                        if ($relation->requiresPivot()) {
                            $tablePivotRelations[] = $relation;
                        }
                    }
                }

                // Add column update
                if (!empty($fieldsForUpdate)) {
                    foreach ($fieldsForUpdate as $fieldTemplate) {
                        if ($fieldTemplate instanceof FieldUpdateTemplate) {
                            FileContentHelper::addLinesAndIndent($content, 1, 3);
                            $content .= "\$table->{$fieldTemplate->getLaravelType()}('{$fieldTemplate->getFieldName()}')";
                            if ($fieldTemplate->defaultValueChanged()) { $content .= "->default('{$fieldTemplate->getDefaultValue()}')"; }
                            if ($fieldTemplate->dropDefault()) { $content .= '->default(null)'; }
                            if ($fieldTemplate->toNullable()) { $content .= '->nullable()'; }
                            if ($fieldTemplate->toNotNullable()) { $content .= '->nullable(false)'; }
                            $content .= "->change();";
                        }
                    }
                }

                // Add column removal
                if (!empty($fieldsForRemoval)) {
                    foreach ($fieldsForRemoval as $field) {
                        FileContentHelper::addLinesAndIndent($content, 1, 3);
                        $content .= "\$table->dropColumn('{$field->getName()}');";
                    }
                }

                FileContentHelper::addLinesAndIndent($content, 1, 2);
                $content .= '});';
            }

            foreach ($tablePivotRelations as $relation) {
                $content .= $relation->compilePivotMigration(2);
            }
        }

        // Custom migration code for table removal
        $tablesForRemoval = $modelMigrationTemplate->getTablesForRemoval();
        if (!empty($tablesForRemoval)) {
            FileContentHelper::addLinesAndIndent($content, 1, 2);
            $content .= "\Schema::enableForeignKeyConstraints();";
            foreach ($tablesForRemoval as $tableName) {
                $relationsForRemoval = $modelMigrationTemplate->getTableForRemovalRelationsForRemoval($tableName);

                // Add relation removal
                if (!empty($relationsForRemoval)) {
                    foreach ($relationsForRemoval as $relation) {
                        if ($relation instanceof ModelRelationCanBeDropped) {
                            FileContentHelper::addNewLines($content, 1);
                            $content .= $relation->compileDrop(2);
                        }
                    }
                }

                FileContentHelper::addLinesAndIndent($content, 1, 2);
                $content .= "\Schema::dropIfExists('{$tableName}');";
            }
            FileContentHelper::addLinesAndIndent($content, 1, 2);
            $content .= "\Schema::disableForeignKeyConstraints();";
        }

        // Ending the UP function
        FileContentHelper::addLinesAndIndent($content);
        $content .= '}';
        FileContentHelper::addNewLines($content);

        // Starting the Down function
        FileContentHelper::addLinesAndIndent($content);
        $content .= 'public function down()';
        FileContentHelper::addLinesAndIndent($content);
        $content .= '{';

        if (!empty($tablesForCreation)) {
            // Droping all data tables
            foreach ($tablesForCreation as $tableName) {
                FileContentHelper::addLinesAndIndent($content, 1, 2);
                $content .= "Schema::drop('{$tableName}');";
            }
        }

        FileContentHelper::addLinesAndIndent($content);
        $content .= '}';
        FileContentHelper::addNewLines($content);

        $content .= '}';

        return $content;
    }
}