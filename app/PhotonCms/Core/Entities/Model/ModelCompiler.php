<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelCompilerInterface;
use Photon\PhotonCms\Core\Helpers\StringConversionsHelper;

use Photon\PhotonCms\Core\Helpers\FileContentHelper;

class ModelCompiler implements ModelCompilerInterface
{
    public function compile(ModelTemplateInterface $template)
    {
        if (!$template instanceof ModelTemplateInterface) {
            throw new PhotonException('ILLEGAL_CLASS_INSTANCE', ['expected' => 'Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface']);
        }

        $content = '<?php';
        $attributes = $template->getAttributes();

        // Adding namespace
        $namespace = $template->getNamespace();
        if ($namespace !== '') {
            FileContentHelper::addNewLines($content, 2);
            $content .= "namespace $namespace;";
        }

        // Adding usage
        $uses = $template->getUses();
        if (!empty($uses)) {

            FileContentHelper::addNewLines($content);

            foreach ($uses as $use) {
                FileContentHelper::addNewLines($content);
                $content .= "use $use;";
            }
        }

        // Defining the class
        FileContentHelper::addNewLines($content, 2);
        $content .= 'class ' . $template->getModelName();

        // Adding inheritance
        if ($template->getInheritance() !== '') {
            $content .= ' extends ' . $template->getInheritance();
        }

        // Implementing interfaces
        if ($template->hasImplementations()) {
            $content .= " implements ".implode(', ', $template->getImplementations());
        }

        // Starting the class
        FileContentHelper::addNewLines($content);
        $content .= '{';
        FileContentHelper::addNewLines($content);

        // Adding traits
        if ($template->hasTraits()) {
            FileContentHelper::addNewLines($content);
            foreach ($template->getTraits() as $trait) {
                FileContentHelper::addIndent($content);
                $content .= "use {$trait};";
                FileContentHelper::addNewLines($content);
            }
        }

        // Defining the table
        FileContentHelper::addNewLines($content);
        FileContentHelper::addIndent($content);
        $content .= 'protected $hidden = [];';

        // Defining the table
        FileContentHelper::addNewLines($content);
        FileContentHelper::addIndent($content);
        $content .= 'protected $table = ' . "'" . $template->getTableName() . "';";

        // Script for relation maintenance
        FileContentHelper::addNewLines($content);
        $content .= '
    // Relation maintenance
    protected $relationsForUpdate = [];

    protected $showRelations = false;

    public function showRelations()
    {
        $this->showRelations = true;
    }

    public function addRelationForUpdate($relationName, $value)
    {
        $this->relationsForUpdate[$relationName] = $value;
    }

    public function save(array $options = [])
    {
        parent::save($options);
        foreach ($this->relationsForUpdate as $attributeName => $value) {
            $relationName = $attributeName.\'_relation\';
            $this->$relationName()->sync($value);
        }
        return true;
    }

    public function delete()
    {';

        $relations = $template->getRelations();
        foreach ($relations as $relation) {
            if ($relation->requiresPivot()) {
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= '$this->'.$relation->getRelationName().'_relation()->detach();';
            }
        }

        $content .= '

        parent::delete();
    }';

        // Date attributes
        $dates = [];
        foreach ($attributes as $attribute) {
            if ($attribute->isDate()) {
                $dates[] = $attribute->getName();
            }
        }
        if (!empty($dates)) {
            FileContentHelper::addLinesAndIndent($content);
            $content .= "protected \$dates = ['".implode("','", $dates)."'];";
            FileContentHelper::addLines($content);
        }

        // Adding relation methods
        FileContentHelper::addLinesAndIndent($content, 2, 1);
        $content .= '// Relation definitions';

        foreach ($relations as $relation) {
            $content .= $relation->compile(1);
        }

        // Getters and setters
        FileContentHelper::addNewLines($content);
        FileContentHelper::addIndent($content);
        $content .= '// Getters and Setters';

        if ($template->usesGettersAndSetters()) {

            // Inserting regular setters and getters
            FileContentHelper::addNewLines($content);
            foreach ($attributes as $attribute) {
                FileContentHelper::addIndent($content);
                $content .= 'public function setAttr'.StringConversionsHelper::snakeCaseToCamelCase($attribute->getName()).'($'.$attribute->getName().')';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeSetterCode($attribute->getName());
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
                FileContentHelper::addIndent($content);
                $content .= 'public function getAttr'.StringConversionsHelper::snakeCaseToCamelCase($attribute->getName()).'()';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeGetterCode($attribute->getName());
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
            }
            foreach ($relations as $relation) {
                FileContentHelper::addIndent($content);
                $content .= 'public function setAttr'.StringConversionsHelper::snakeCaseToCamelCase($relation->getRelationName()).'($'.$relation->getRelationName().')';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeSetterCode($relation->getRelationName());
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
                FileContentHelper::addIndent($content);
                $content .= 'public function getAttr'.StringConversionsHelper::snakeCaseToCamelCase($relation->getRelationName()).'()';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeGetterCode($relation->getRelationName());
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
            }

            // Inserting setters for all attributes
            FileContentHelper::addIndent($content);
            $content .= 'public function setAll(array &$data)';
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content);
            $content .= '{';
            FileContentHelper::addNewLines($content);

            foreach ($attributes as $attribute) {
                if(!$attribute->getFieldType()->is_system) {
                    FileContentHelper::addIndent($content, 2);
                    $content .= 'if (array_key_exists(\''.$attribute->getName().'\', $data)) { $this->setAttr'.StringConversionsHelper::snakeCaseToCamelCase($attribute->getName()).'($data[\''.$attribute->getName().'\']); }';
                    FileContentHelper::addNewLines($content);
                }
            }

            foreach ($relations as $relation) {
                if(!$relation->getFieldType()->is_system) {
                    FileContentHelper::addIndent($content, 2);
                    $content .= 'if (array_key_exists(\''.$relation->getRelationName().'\', $data)) { $this->setAttr'.StringConversionsHelper::snakeCaseToCamelCase($relation->getRelationName()).'($data[\''.$relation->getRelationName().'\']); }';
                    FileContentHelper::addNewLines($content);
                }
            }

            // Extend the setter
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content, 2);
            $content .= '$this->callAvailableMagicSetterExtension($data);';
            FileContentHelper::addNewLines($content);

            FileContentHelper::addIndent($content);
            $content .= '}';
            FileContentHelper::addNewLines($content, 2);

            // Inserting getter for all attributes
            FileContentHelper::addIndent($content);
            $content .= 'public function getAll()';
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content);
            $content .= '{';
            FileContentHelper::addNewLines($content);

            // Regular fields
            FileContentHelper::addIndent($content, 2);
            $content .= '$data = [';
            FileContentHelper::addNewLines($content);

            foreach ($attributes as $attribute) {
                if (!$attribute->getFieldType()->isRelation()) {
                    FileContentHelper::addIndent($content, 3);
                    $content .= '\''.$attribute->getName().'\' => $this->getAttr'.StringConversionsHelper::snakeCaseToCamelCase($attribute->getName()).'(),';
                    FileContentHelper::addNewLines($content);
                }
            }

            FileContentHelper::addIndent($content, 2);
            $content .= '];';

            // Relations as full objects
            FileContentHelper::addNewLines($content, 2);
            FileContentHelper::addIndent($content, 2);
            $content .= 'if ($this->showRelations) {';
            FileContentHelper::addNewLines($content);

            FileContentHelper::addIndent($content, 3);
            $content .= '$data += [';
            FileContentHelper::addNewLines($content);

            foreach ($relations as $relation) {
                FileContentHelper::addIndent($content, 4);
                $content .= '\''.$relation->getRelationName().'\' => $this->getAttr'.StringConversionsHelper::snakeCaseToCamelCase($relation->getRelationName()).'(),';
                FileContentHelper::addNewLines($content);
            }

            FileContentHelper::addIndent($content, 3);
            $content .= '];';

            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content, 2);
            $content .= '}';

            // Relations as IDs
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content, 2);
            $content .= 'else {';
            FileContentHelper::addNewLines($content);

            FileContentHelper::addIndent($content, 3);
            $content .= '$data += [';
            FileContentHelper::addNewLines($content);

            foreach ($relations as $relation) {
                FileContentHelper::addIndent($content, 4);
                if ($relation->requiresPivot()) {
                    $content .= '\''.$relation->getRelationName().'\' => [],';
                }
                else {
                    $content .= '\''.$relation->getRelationName().'\' => $this->'.$relation->getRelationName().',';
                }
                FileContentHelper::addNewLines($content);
            }

            FileContentHelper::addIndent($content, 3);
            $content .= '];';

            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content, 2);
            $content .= '}';

            // Extend the getter
            FileContentHelper::addNewLines($content, 2);
            FileContentHelper::addIndent($content, 2);
            $content .= '$data = array_merge($data, $this->callAvailableMagicGetterExtension());';

            FileContentHelper::addNewLines($content, 2);
            FileContentHelper::addIndent($content, 2);
            $content .= 'return $data;';

            // End of the getter
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content);
            $content .= '}';

        }

        // Closing the class
        FileContentHelper::addNewLines($content);
        $content .= '}';

        return $content;
    }
}