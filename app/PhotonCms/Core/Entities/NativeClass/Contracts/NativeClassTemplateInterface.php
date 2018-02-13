<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass\Contracts;

use Photon\PhotonCms\Core\Entities\NativeClassAttribute\NativeClassAttribute;

interface NativeClassTemplateInterface
{

    /**
     * Returns a specified fileName for a model file.
     * Empty string will be returned for no fileName.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Sets a specific file name for a model file.
     *
     * @param string $fileName
     */
    public function setFilename($fileName);

    /**
     * Checks if the model fileName has been specified.
     *
     * @return bool
     */
    public function usesFilename();

    /**
     * Returns the path on which the file should be saved.
     * Empty string will be returned for no path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets a path on which the file should be saved.
     *
     * @param string $path
     */
    public function setPath($path);

    /**
     * Checks if the path has been speicifed for the file.
     *
     * @return bool
     */
    public function usesPath();

    /**
     * Namespace getter.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Namespace setter.
     *
     * @param string $namespace
     */
    public function setNamespace($namespace);

    /**
     * Getter for class namespaces used in the model.
     *
     * @return array
     */
    public function getUses();

    /**
     * Setts an array of class namespaces which will be used in the model.
     *
     * @param array $uses
     */
    public function setUses(array $uses);

    /**
     * Adds a class namespace which is neccessary for the model to the array.
     * The value is added to the existing array.
     *
     * @param string $use
     */
    public function assignUse($use);

    public function hasUses();

    /**
     * Model name getter.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Model name setter.
     *
     * @param string $className
     */
    public function setClassName($className);

    /**
     * Returns a compiled class name with a namespace.
     *
     * @return string
     */
    public function getFullClassName();

    /**
     * Inheritance getter.
     * Returns the name of the class which will be extended.
     *
     * @return string
     */
    public function getInheritance();

    /**
     * Inheritance setter.
     * Sets the name of the class which will be extended.
     *
     * @param string $extends
     */
    public function setInheritance($extends);

    /**
     * Indicates wether the class extends another class.
     *
     * @return boolean
     */
    public function hasInheritance();

    /**
     * Returns an array of all interface implementations.
     *
     * @return array
     */
    public function getImplementations();

    /**
     * Sets interface implementations for the class.
     *
     * @param array $interfaces
     */
    public function setImplementations(array $interfaces);

    /**
     * Adds an interface implementation to the class.
     * Requres a full interface name with the namespace.
     *
     * @param string $interface
     */
    public function addImplementation($interface);

    /**
     * Indicates if the class has any interface implementations.
     *
     * @return boolean
     */
    public function hasImplementations();

    /**
     * Gets all assigned traits which will be used in the class.
     *
     * @return array
     */
    public function getTraits();

    /**
     * Sets an array of traits which will be used throughout the class.
     * This will overwrite any traits which were assigned previously!
     *
     * @param array $traits
     */
    public function setTraits(array $traits);

    /**
     * Assigns an additional trait to already assigned traits for the class usage.
     *
     * @param string $trait
     */
    public function assignTrait($trait);

    /**
     * Indicates if the class uses any traits.
     *
     * @return boolean
     */
    public function hasTraits();

    /**
     * Returns an array of all assigned class attributes as keypairs.
     * [attributeName] => [attributeType]
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Adds an array of attributes to the class model.
     *
     * @param array $attributes
     */
    public function addAttributes(array $attributes);

    /**
     * Adds an attribute to the class model.
     *
     * @param string $name
     * @param type $type
     */
    public function addAttribute(NativeClassAttribute $attribute);

    /**
     * Provides the body of an attribute setter.
     *
     * @param string $fieldName
     * @return string
     */
    public function getAttributeSetterCode($attributeName);

    /**
     * Provides the body of an attribute getter.
     *
     * @param string $fieldName
     * @return string
     */
    public function getAttributeGetterCode($attributeName);

    /**
     * Setts building of getters and setters to true.
     */
    public function useGettersAndSetters();

    /**
     * Checks usage of getters and setters in the class.
     *
     * @return boolean
     */
    public function usesGettersAndSetters();
}