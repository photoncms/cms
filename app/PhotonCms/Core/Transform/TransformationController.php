<?php

namespace Photon\PhotonCms\Core\Transform;

use App;
use Cyvelnet\Laravel5Fractal\FractalServices;
use Baum\Extensions\Eloquent\Collection as BaumCollection;
use Illuminate\Database\Eloquent\Collection as IlluminateCollection;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;

class TransformationController
{
    /**
     * Array of Class to its Transformer relations.
     *
     * @var array
     */
    private $transformationMap = [
        'Photon\\PhotonCms\\Core\\Entities\\Menu\\Menu'                 => 'Photon\\PhotonCms\\Core\\Entities\\Menu\\MenuTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\MenuItem\\MenuItem'         => 'Photon\\PhotonCms\\Core\\Entities\\MenuItem\\MenuItemTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\MenuLinkType\\MenuLinkType' => 'Photon\\PhotonCms\\Core\\Entities\\MenuLinkType\\MenuLinkTypeTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\Module\\Module'             => 'Photon\\PhotonCms\\Core\\Entities\\Module\\ModuleTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\ModuleType\\ModuleType'     => 'Photon\\PhotonCms\\Core\\Entities\\ModuleType\\ModuleTypeTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\FieldType\\FieldType'       => 'Photon\\PhotonCms\\Core\\Entities\\FieldType\\FieldTypeTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\Field\\Field'               => 'Photon\\PhotonCms\\Core\\Entities\\Field\\FieldTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\Node\\Node'                 => 'Photon\\PhotonCms\\Core\\Entities\\Node\\NodeTransformer',
        'Photon\\PhotonCms\\Core\\Entities\\User\\User'                 => 'Photon\\PhotonCms\\Core\\Entities\\User\\UserTransformer',
        'Illuminate\\Validation\\Validator'                             => 'Photon\\PhotonCms\\Core\\Entities\\Validation\\ValidationTransformer',
        'Illuminate\\Notifications\\DatabaseNotification'               => 'Photon\\PhotonCms\\Core\\Entities\\Notification\\NotificationTransformer',
        'Illuminate\\Pagination\\LengthAwarePaginator'                  => 'Photon\\PhotonCms\\Core\\Entities\\Pagination\\PaginationTransformer',
    ];

    /**
     * Transformer for all dynamic modules.
     *
     * @var string
     */
    private $dynamicModelTransformer = 'Photon\\PhotonCms\\Core\\Entities\\DynamicModule\\DynamicModuleTransformer';

    /**
     * Fractal services for transformations.
     *
     * @var FractalServices
     */
    private $fractalServices;

    /**
     * Constructor
     *
     * @param FractalServices $fractalServices
     */
    public function __construct(FractalServices $fractalServices)
    {
        $this->fractalServices = $fractalServices;
        $this->exceptionHandler = App::make('Illuminate\Contracts\Debug\ExceptionHandler');
    }

    /**
     * Transforms all mappable objects provided in input data.
     *
     * @param array|object $data
     * @return array|object
     */
    public function transform($data)
    {
        if (is_array($data) || $data instanceof BaumCollection || $data instanceof IlluminateCollection) {
            $this->transformRecursively($data);
        } else if (is_object($data)) {
            $data = $this->transformObject($data);
            $this->transformRecursively($data);
        }

        return $data;
    }

    /**
     * Recursively checks for objects inside the input array and calls $this->transformObject() to transform all recursively.
     * IMPORTANT - If an item of the Collection is unset, the collection returns an associative array instead of a numbered array.
     * This method contains a fix for a said situation, and $fixedArray is used to replace any collections at output.
     *
     * @param array $array
     */
    private function transformRecursively(&$array)
    {
        $fixedArray = []; // Collection fix
        foreach ($array as $key => $item) {
            if (is_object($item)) {
                $item = $this->transformObject($item);
            }

            if (is_array($item) || $item instanceof BaumCollection || $item instanceof IlluminateCollection) {
                $this->transformRecursively($item);
                if ($array instanceof BaumCollection || $array instanceof IlluminateCollection) { // Collection fix
                    $fixedArray[] = $item;
                }
                else{
                    $array[$key] = $item;
                }
            }
        }
        if (!empty($fixedArray)) { // Collection fix
            $array = $fixedArray;
        }
    }

    /**
     * Transforms an object using $this->transformationMap and FractalServices to array.
     * If the object class is not mapped, the object is passed back unchanged.
     *
     * @param object $object
     * @return array
     */
    private function transformObject($object)
    {
        // Handle exceptions
        if ($object instanceof \Exception) {
            $object = $this->exceptionHandler->exceptionToJson($object)->getOriginalContent();
        }
        // Handle Dynamic modules
        else if ($object instanceof DynamicModuleInterface) {
            $transformer = new $this->dynamicModelTransformer();
            $data = $this->fractalServices->item($object, $transformer)->getArray();
            return (isset($data['data']))
                ? $data['data']
                : $data;
        }
        // Handle other objects
        else {
            $className = (string) get_class($object);

            if (isset($this->transformationMap[$className])) {
                $transformer = new $this->transformationMap[$className]();
                $data = $this->fractalServices->item($object, $transformer)->getArray();
                return (isset($data['data']))
                    ? $data['data']
                    : $data;
            }
        }

        return $object;
    }

    /**
     * Transforms the complete object into an array using the objects transformer.
     *
     * @param mixed $object
     * @return array
     */
    public function objectFullTransform($object)
    {
        $className = (string) get_class($object);
        if (isset($this->transformationMap[$className])) {
            $transformer = new $this->transformationMap[$className]();

            if ($transformer instanceof TransformerFullTransformInterface) {
                return $transformer->fullTransform($object);
            }
        }
        else {
            return $this->transformObject($object);
        }
    }

    /**
     * Fully transforms the object and convertsany data to JSON or string castable data.
     *
     * @param mixed $object
     * @return array
     */
    public function objectFullTransformConverted($object)
    {
        $className = (string) get_class($object);
        // Handle Dynamic modules
        if ($object instanceof DynamicModuleInterface) {
            $transformer = new $this->dynamicModelTransformer();
            $data = $this->fractalServices->item($object, $transformer)->getArray();
            return (isset($data['data']))
                ? $data['data']
                : $data;
        }
        else if (isset($this->transformationMap[$className])) {
            $transformer = new $this->transformationMap[$className]();

            if ($transformer instanceof TransformerFullTransformConvertedInterface) {
                return $transformer->fullTransformConverted($object);
            }
        }
        else {
            return $this->transformObject($object);
        }
    }
}
