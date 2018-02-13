<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use Photon\PhotonCms\Core\Helpers\FileContentHelper;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassCompilerInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;

class NativeClassCompiler implements NativeClassCompilerInterface
{
    /**
     * Compiles class file contents using class template instance.
     *
     * @param NativeClassTemplateInterface $template
     * @return string
     */
    public function compile(NativeClassTemplateInterface $template)
    {
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

        // Adding interface usage
        $interfaces = $template->getImplementations();
        if (!empty($interfaces)) {

            FileContentHelper::addNewLines($content);

            foreach ($interfaces as $interface) {
                FileContentHelper::addNewLines($content);
                $content .= "use $interface;";
            }
        }

        // Adding trait usage
        $traits = $template->getTraits();
        if (!empty($traits)) {

            FileContentHelper::addNewLines($content);

            foreach ($traits as $trait) {
                FileContentHelper::addNewLines($content);
                $content .= "use $trait;";
            }
        }

        // Defining the class
        FileContentHelper::addNewLines($content, 2);
        $content .= 'class ' . $template->getClassName();

        // Adding inheritance
        if ($template->getInheritance() !== '') {
            $content .= ' extends ' . $template->getInheritance();
        }

        // Implementing interfaces
        if (!empty($interfaces)) {
            $interfaceCounter = 0;
            foreach ($interfaces as $interface) {
                $parsedInterface = explode('\\',$interface);
                if ($interfaceCounter === 0) {
                    $content .= " implements ".end($parsedInterface);
                }
                else if ($interfaceCounter > 0) {
                    $content .= ", ".end($parsedInterface);
                }
                $interfaceCounter++;
            }
        }

        // Starting the class
        FileContentHelper::addNewLines($content);
        $content .= '{';
        FileContentHelper::addNewLines($content);

        // Adding traits
        if (!empty($traits)) {
            FileContentHelper::addNewLines($content);
            foreach ($traits as $trait) {
                FileContentHelper::addIndent($content);
                $parsedTrait = explode('\\',$trait);
                $content .= "use ".end($parsedTrait).";";
                FileContentHelper::addNewLines($content);
            }
        }

        if ($template->usesGettersAndSetters()) {
            // Getters and setters
            FileContentHelper::addNewLines($content);
            FileContentHelper::addIndent($content);
            $content .= '// Getters and Setters';

            // Inserting regular setters and getters
            FileContentHelper::addNewLines($content);
            foreach ($attributes as $attributeName => $attributeType) {
                FileContentHelper::addIndent($content);
                $content .= 'public function setAttr'.ucfirst($attributeName).'($'.$attributeName.')';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeSetterCode($attributeName);
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
                FileContentHelper::addIndent($content);
                $content .= 'public function getAttr'.ucfirst($attributeName).'()';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '{';
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content, 2);
                $content .= $template->getAttributeGetterCode($attributeName);
                FileContentHelper::addNewLines($content);
                FileContentHelper::addIndent($content);
                $content .= '}';
                FileContentHelper::addNewLines($content, 2);
            }
        }

        // Closing the class
        FileContentHelper::addNewLines($content);
        $content .= '}';

        return $content;
    }
}