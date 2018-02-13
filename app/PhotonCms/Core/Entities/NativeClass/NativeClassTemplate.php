<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;
use Photon\PhotonCms\Core\Entities\NativeClassAttribute\NativeClassAttribute;
use Photon\PhotonCms\Core\Helpers\ClassNameHelper;

class NativeClassTemplate implements NativeClassTemplateInterface
{

    /**
     * Name of the class file with the extension.
     *
     * @var string
     */
    protected $fileName = '';

    /**
     * Indicates if the class file name is additionally set (true) or should be set automatically (false).
     *
     * @var boolean
     */
    protected $usesFileName = false;

    /**
     * Path on which the file will be saved.
     *
     * @var string
     */
    protected $path = '';

    /**
     * Namespace of the model.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Array of neccessary class namespaces.
     *
     * @var array
     */
    protected $uses = [];
    
    /**
     * Name of the class.
     *
     * @var string
     */
    protected $className = '';
    
    /**
     * Class name of the parent class.
     *
     * @var string
     */
    protected $extends = '';

    /**
     * Array of interface names with namespaces.
     * Each array entry is a single string of the interface name with its namespace.
     *
     * @var array
     */
    protected $implements = [];

    /**
     * Array of all traits which will be used within the class.
     *
     * @var array
     */
    protected $traits = [];
    
    /**
     * Array of object attributes.
     * Contains attributes in following format [attributeName]=>[fieldType]
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Flag which determines if getters and setters should be created for this model class.
     *
     * @var boolean
     */
    protected $useGettersAndSetters = false;

    /**
     * Returns a specified fileName for a model file.
     * Empty string will be returned for no fileName.
     *
     * @return string
     */
    public function getFilename()
    {
        if (!$this->usesFileName) {
            return $this->className.'.php';
        }
        return $this->fileName;
    }

    /**
     * Sets a specific file name for a model file.
     *
     * @param string $fileName
     */
    public function setFilename($fileName)
    {
        $this->fileName = $fileName;
        $this->usesFileName = true;
    }

    /**
     * Checks if the model fileName has been specified.
     *
     * @return bool
     */
    public function usesFilename()
    {
        return $this->usesFileName;
    }

    /**
     * Returns the path on which the file should be saved.
     * Empty string will be returned for no path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets a path on which the file should be saved.
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Checks if the path has been speicifed for the file.
     *
     * @return bool
     */
    public function usesPath()
    {
        return $this->path !== '';
    }

    /**
     * Namespace getter.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Namespace setter.
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Getter for class namespaces used in the model.
     *
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Setts an array of class namespaces which will be used in the model.
     *
     * @param array $uses
     */
    public function setUses(array $uses)
    {
        $this->uses = $uses;
    }

    /**
     * Adds a class namespace which is neccessary for the model to the array.
     * The value is added to the existing array.
     *
     * @param string $use
     */
    public function assignUse($use)
    {
        if (!in_array($use, $this->uses)) {
            $this->uses[] = $use;
        }
    }

    public function hasUses()
    {
        return is_array($this->uses) && !empty($this->uses);
    }

    /**
     * Model name getter.
     * 
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Model name setter.
     * 
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns a compiled class name with a namespace.
     *
     * @return string
     */
    public function getFullClassName()
    {
        return '\\'.$this->namespace.'\\'.$this->className;
    }

    /**
     * Inheritance getter.
     * Returns the name of the class which will be extended.
     * 
     * @return string
     */
    public function getInheritance()
    {
        return $this->extends;
    }

    /**
     * Inheritance setter.
     * Sets the name of the class which will be extended.
     * 
     * @param string $extends
     */
    public function setInheritance($extends)
    {
        $this->extends = ClassNameHelper::getClassNameFromNamespace($extends);
        $this->assignUse($extends);
    }

    /**
     * Indicates wether the class extends another class.
     *
     * @return boolean
     */
    public function hasInheritance()
    {
        return (bool) $this->extends;
    }

    /**
     * Returns an array of all interface implementations.
     *
     * @return array
     */
    public function getImplementations()
    {
        return $this->implements;
    }

    /**
     * Sets interface implementations for the class.
     *
     * @param array $interfaces
     */
    public function setImplementations(array $interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addImplementation($interface);
        }
    }

    /**
     * Adds an interface implementation to the class.
     * Requres a full interface name with the namespace.
     *
     * @param string $interface
     */
    public function addImplementation($interface)
    {
        $this->implements[] = ClassNameHelper::getClassNameFromNamespace($interface);
        $this->assignUse($interface);
    }

    /**
     * Indicates if the class has any interface implementations.
     *
     * @return boolean
     */
    public function hasImplementations()
    {
        return is_array($this->implements) && !empty($this->implements);
    }

    /**
     * Gets all assigned traits which will be used in the class.
     *
     * @return array
     */
    public function getTraits()
    {
        $traits = $this->traits;

        return $traits;
    }

    /**
     * Sets an array of traits which will be used throughout the class.
     * This will overwrite any traits which were assigned previously!
     *
     * @param array $traits
     */
    public function setTraits(array $traits)
    {
        foreach ($traits as $trait) {
            $this->assignTrait($trait);
        }
    }

    /**
     * Assigns an additional trait to already assigned traits for the class usage.
     *
     * @param string $trait
     */
    public function assignTrait($trait)
    {
        $this->traits[] = ClassNameHelper::getClassNameFromNamespace($trait);
        $this->assignUse($trait);
    }

    /**
     * Indicates if the class uses any traits.
     *
     * @return boolean
     */
    public function hasTraits()
    {
        return is_array($this->traits) && !empty($this->traits);
    }

    /**
     * Returns an array of all assigned class attributes as keypairs.
     * [attributeName] => [attributeType]
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Adds an array of attributes to the class model.
     *
     * @param array $attributes
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
    }

    /**
     * Adds an attribute to the class model.
     *
     * @param string $name
     * @param type $type
     */
    public function addAttribute(NativeClassAttribute $attribute)
    {
        $this->attributes[$attribute->getName()] = $attribute;
    }

    /**
     * Provides the body of an attribute setter.
     *
     * @param string $fieldName
     * @return string
     */
    public function getAttributeSetterCode($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return '$this->'.$attributeName.' = $'.$attributeName.';';
        }
    }

    /**
     * Provides the body of an attribute getter.
     *
     * @param string $fieldName
     * @return string
     */
    public function getAttributeGetterCode($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return 'return $this->'.$attributeName.';';
        }
    }

    /**
     * Setts building of getters and setters to true.
     */
    public function useGettersAndSetters()
    {
        $this->useGettersAndSetters = true;
    }

    /**
     * Checks usage of getters and setters in the class.
     *
     * @return boolean
     */
    public function usesGettersAndSetters()
    {
        return $this->useGettersAndSetters;
    }
}