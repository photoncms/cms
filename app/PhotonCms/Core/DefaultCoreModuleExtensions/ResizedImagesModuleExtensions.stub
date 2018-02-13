<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\Image\ImageRepository;
use Photon\PhotonCms\Core\Entities\Image\ImageFactoryFactory;
use Photon\PhotonCms\Core\Entities\Image\ImageGatewayFactory;

use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasExtensionFunctions;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasGetterExtension;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete;

class ResizedImagesModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHasExtensionFunctions,
    ModuleExtensionHasGetterExtension,
    ModuleExtensionHandlesPostDelete
{
    public function __construct(
        ResponseRepository $responseRepository,
        ImageRepository $imageRepository
    )
    {
        $this->imageRepository = $imageRepository;
        parent::__construct($responseRepository);
    }

    /*****************************************************************
     * These functions represent the event handlers for create/update/delete actions over a dynamic module model.
     * Their return is not handled, so returning responses from here is useless.
     * Each function can be interrupted by throwing an exception. Throwing an exception will also stop the whole process. For
     * example, if a preCreate function throws an exception, this means, since the object hasn't been saved yet, the object
     * will never be saved at all.
     */
    public function postDelete($item)
    {
        \Storage::disk('assets')->delete($item->storage_file_name);
    }


    /*****************************************************************
     * Following funcitons extend models getAll() and setAll() methods.
     * Getter extension should return an array which will be appended to the array compiled by the model.
     * Setter extension should set any additional attributes for the model which are not automatically set
     * by the setAll() method.
     */
    public function executeGetterExtension($entry)
    {
        return [
            'file_url' => asset("storage/assets/" . $entry->storage_file_name)
        ];
    }

    /*****************************************************************
     * This function if required by the interface, is used for outputting pre-compiled extension function calls through the API.
     * The result should be in form of an associative array with keys representing the human-readable name of the function call
     * and the value should be the API path for the call.
     *
     * The action_name in the path will be capitalized and added a prefix 'call'. For example, 'myAction' action name in the
     * path will result in calling of 'callMyAction'.
     * This is to prevent any other public methods from being called through the api illegally.
     */
    public function getExtensionFunctionCalls($item)
    {
        return [
            //'My Function' => '/api/extension_call/{table_name}/{item_id}/{action_name}',
            'Test' => "/api/extension_call/resized_images/{$item->id}/rebuild"
        ];
    }

    /*****************************************************************
     * Following functions represent all of the extended custom functionality. Each of these functions should be 'registered'
     * within the $this->getExtensionFunctionCalls() return array. Each function name will be used without the 'call'prefix and
     * ucfirst.
     */
    public function callRebuild($item, $x, $y, $width, $height)
    {
        // Prepare tools and resources
        $imageFactory = ImageFactoryFactory::make(env('IMAGE_SOFTWARE'));

        $originalImageFileName = $item->image_relation->storage_file_name;
        $path = config('filesystems.disks.assets.root').'/'.$originalImageFileName;
        $image = $imageFactory::makeFromFile($path);

        $imageGateway = ImageGatewayFactory::make(env('IMAGE_SOFTWARE'), $path);

        if (!$image) {
            throw new PhotonException('FAILED_TO_LOAD_IMAGE_RESOURCE');
        }

        // find original image widht and height
        $inWidth = $this->imageRepository->getImageWidth($image, $imageGateway);
        $inHeight = $this->imageRepository->getImageHeight($image, $imageGateway);

        $imageSize = $item->image_size_relation;

        // handle auto
        $outWidth = ($imageSize->width == 0) // 0 means auto
            ? (int) ($width * ($imageSize->height / $height))
            : $imageSize->width;
        $outHeight = ($imageSize->height == 0) // 0 means auto
            ? (int) ($height * ($imageSize->width / $width))
            : $imageSize->height;

        return $this->regenerateResizedImage($x, $y, $width, $height, $outWidth, $outHeight, $image, $originalImageFileName, 1, $imageGateway, $imageSize, $item);
    }

    private function regenerateResizedImage($x, $y, $width, $height, $outWidth, $outHeight, $image, $originalImageFileName, $databaseFlag, $imageGateway, $imageSize = null, $item = null)
    {
        // Crop and resize the image
        $croppedAndResizedImage = $this->imageRepository->cropAndResize(
            $image,
            $x,
            $y,
            $width,
            $height,
            $outWidth,
            $outHeight,
            $imageGateway
        );

        // Prepare new filename
        // $suffix = \Photon\PhotonCms\Core\Helpers\StringConversionsHelper::stringToAlphaNumDash($imageSize->name);
        // $newFileName = pathinfo($originalImageFileName, PATHINFO_FILENAME)."_{$suffix}.".pathinfo($originalImageFileName, PATHINFO_EXTENSION);
        $suffix = $outWidth . "x" . $outHeight;
        $newFileName = pathinfo($originalImageFileName, PATHINFO_FILENAME)."_{$suffix}.".pathinfo($originalImageFileName, PATHINFO_EXTENSION);

        // Delete the old file if it exists
        if($item)
            \Storage::disk('assets')->delete($item->storage_file_name);

        // Save the new file
        $this->imageRepository->saveImage($croppedAndResizedImage, $newFileName, $imageGateway);

        list($imageWidth, $imageHeight, $type, $attr) = getimagesize(config('filesystems.disks.assets.root').'/'.$newFileName);

        if(!$databaseFlag) 
            return true;

        // Update entry data
        $item->top_x = $x;
        $item->top_y = $y;
        $item->width = $width;
        $item->height = $height;
        $item->storage_file_name = $newFileName;
        $item->file_size = filesize(config('filesystems.disks.assets.root').'/'.$newFileName);
        $item->image_width = $imageWidth;
        $item->image_height = $imageHeight;
        $item->mime_type = image_type_to_mime_type($type);
        $item->save();

        return $this->responseRepository->make('RESIZED_IMAGE_REBUILD_SUCCESS', ['resized_image' => $item]);
    }
}