<?php

namespace Photon\PhotonCms\Dependencies\ModuleExtensions;

use Illuminate\Http\Response;
use \Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\BaseDynamicModuleExtension;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Photon\PhotonCms\Core\Entities\FileSystem\FileSystemRepository;
use Photon\PhotonCms\Dependencies\DynamicModels\ResizedImages;
use Photon\PhotonCms\Dependencies\DynamicModels\ImageSizes;
use Photon\PhotonCms\Core\Entities\Image\ImageRepository;
use Photon\PhotonCms\Core\Entities\Image\ImageGatewayFactory;
use Photon\PhotonCms\Core\Entities\Image\ImageFactoryFactory;

use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasGetterExtension;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete;
use Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasSetterExtension;
use Photon\PhotonCms\Core\IAPI\IAPI;


/**
 * These are functionality extensions for the Assets module.
 */
class AssetsModuleExtensions extends BaseDynamicModuleExtension implements
    ModuleExtensionHasGetterExtension,
    ModuleExtensionHasSetterExtension,
    ModuleExtensionHandlesPostCreate,
    ModuleExtensionHandlesPostDelete
{

    use ValidatesRequests;

    /**
     * This constructor represents a part of an HMVC architecture.
     * The class is (and should be) instantiated allways through laravel's IoC container, thus
     * providing a possibility to typehint dependency injections into the class constructor.
     *
     * @param ResponseRepository $responseRepository
     */
    public function __construct(
        ResponseRepository $responseRepository,
        FileSystemRepository $fileSystemRepository,
        ImageRepository $imageRepository
    )
    {
        parent::__construct($responseRepository);
        $this->fileSystemRepository = $fileSystemRepository;
        $this->imageRepository = $imageRepository;
    }

    /*****************************************************************
     * These functions represent interrupters for regular dynamic module entry flow.
     * If an instance of \Illuminate\Http\Response is returned, the rest of the flow after it will be interrupted.
     */
    public function interruptCreate()
    {
        $interrupt = parent::interruptCreate();
        if ($interrupt instanceof Response) {
            return $interrupt;
        }

        $validator = \Validator::make($this->requestData, [
            'file' => 'required|file',
        ]);
        if ($validator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        $isImage = true;
        $imageValidator = \Validator::make($this->requestData, [
            'file' => 'image',
        ]);
        if ($imageValidator->fails()) {
            $isImage = false;
        }

        $disk = 'assets';
        $file = $this->requestData['file'];
        $fileNameAndPath = $this->fileSystemRepository->save($file, $disk);

        if ($fileNameAndPath) {
            // Add an entry to the assets module
            $this->requestData['file_name'] = $file->getClientOriginalName();
            $this->requestData['storage_file_name'] = $fileNameAndPath;
            $this->requestData['mime_type'] = $file->getClientMimeType();
            $this->requestData['file_size'] = $file->getClientSize();
            if($isImage) {
                list($width, $height, $type, $attr) = getimagesize(config('filesystems.disks.assets.root').'/'.$fileNameAndPath);
                $this->requestData['image_width'] = $width;
                $this->requestData['image_height'] = $height;
            }
        }
        else {
            throw new PhotonException('FILE_UPLOAD_FAILURE');
        }
    }

    public function interruptDelete($entry)
    {
        $interrupt = parent::interruptDelete($entry);
        if ($interrupt instanceof Response) {
            return $interrupt;
        }
        
        $iapi = new \Photon\PhotonCms\Core\IAPI\IAPI();
        foreach ($entry->resized_images_relation as $resizedImage) {
            $iapi->resized_images($resizedImage->id)->delete();
        }
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

    /**
     * Executed after an entry has been persisted.
     *
     * @param object $item
     * @param object $cloneAfter
     */
    public function postCreate($item, $cloneAfter)
    {
        // Prepare tools and resources
        $imageFactory = ImageFactoryFactory::make(env('IMAGE_SOFTWARE'));

        $originalImageFileName = $item->storage_file_name;
        $path = config('filesystems.disks.assets.root') . '/' . $item->storage_file_name;
        $image = $imageFactory::makeFromFile($path);
        if(!$image)
            return true;

        $imageGateway = ImageGatewayFactory::make(env('IMAGE_SOFTWARE'), $path);

        if (!$image) {
            throw new PhotonException('FAILED_TO_LOAD_IMAGE_RESOURCE');
        }

        $inWidth = $this->imageRepository->getImageWidth($image, $imageGateway);
        $inHeight = $this->imageRepository->getImageHeight($image, $imageGateway);

        $allImageSizes = ImageSizes::all();

        // Make a resized image for each defined size
        foreach ($allImageSizes as $imageSize) {
            
            $outWidth = ($imageSize->width == 0) // 0 means auto
                ? (int) ($inWidth * ($imageSize->height / $inHeight))
                : $imageSize->width;
            $outHeight = ($imageSize->height == 0) // 0 means auto
                ? (int) ($inHeight * ($imageSize->width / $inWidth))
                : $imageSize->height;

            $this->generateResizedImage($inWidth, $inHeight, $outWidth, $outHeight, $image, $originalImageFileName, 1, $imageGateway, $imageSize, $item);
        }
    }

    private function generateResizedImage($inWidth, $inHeight, $outWidth, $outHeight, $image, $originalImageFileName, $databaseFlag, $imageGateway, $imageSize = null, $item = null)
    {

        // Automatically positioning cropping tool to center and calculating cropping position
        list($scaledWidth, $scaledHeight) = $this->imageRepository->calculateResize($inWidth, $inHeight, $outWidth, $outHeight);
        list($x, $y) = $this->imageRepository->calculateCropPosition($scaledWidth, $scaledHeight, $outWidth, $outHeight);

        // Calculating scale ration back to expected image size
        $widthScaleRatio = $scaledWidth/$inWidth;
        $heightScaleRatio = $scaledHeight/$inHeight;

        // Calculating original cropping data
        $originalX = (int) $x/$widthScaleRatio;
        $originalY = (int) $y/$heightScaleRatio;
        $originalWidth = (int) $outWidth/$widthScaleRatio;
        $originalHeight = (int) $outHeight/$heightScaleRatio;

        // Crop and resize the image
        $croppedAndResizedImage = $this->imageRepository->cropAndResize(
            $image,
            $originalX,
            $originalY,
            $originalWidth,
            $originalHeight,
            $outWidth,
            $outHeight,
            $imageGateway
        );

        // Prepare a new file name
        // $suffix = \Photon\PhotonCms\Core\Helpers\StringConversionsHelper::stringToAlphaNumDash($imageSize->name);
        $suffix = $outWidth . "x" . $outHeight;
        $newFileName = pathinfo($originalImageFileName, PATHINFO_FILENAME)."_{$suffix}.".pathinfo($originalImageFileName, PATHINFO_EXTENSION);

        // Save the new image
        $this->imageRepository->saveImage($croppedAndResizedImage, $newFileName, $imageGateway);

        list($width, $height, $type, $attr) = getimagesize(config('filesystems.disks.assets.root').'/'.$newFileName);

        if($databaseFlag) {
            // Create an entry in resized_images
            $resizedImage = new ResizedImages();
            $resizedImage->top_x = $originalX;
            $resizedImage->top_y = $originalY;
            $resizedImage->width = $originalWidth;
            $resizedImage->height = $originalHeight;
            $resizedImage->storage_file_name = $newFileName;
            $resizedImage->image = $item->id;
            $resizedImage->image_size = $imageSize->id;
            $resizedImage->image_width = $width;
            $resizedImage->image_height = $height;
            $resizedImage->file_size = filesize(config('filesystems.disks.assets.root').'/'.$newFileName);
            $resizedImage->mime_type = image_type_to_mime_type($type);
            $resizedImage->save();
        }
    }

    /*****************************************************************
     * Following funcitons extend models getAll() and setAll() methods.
     * Getter extension should return an array which will be appended to the array compiled by the model.
     * Setter extension should set any additional attributes for the model which are not automatically set
     * by the setAll() method.
     */
    public function executeSetterExtension($entry)
    {
        if(isset($this->requestData['storage_file_name'])) {
            $entry->storage_file_name = $this->requestData['storage_file_name'];
        }
        if(isset($this->requestData['mime_type'])) {
            $entry->mime_type = $this->requestData['mime_type'];
        }
        if(isset($this->requestData['file_size'])) {
            $entry->file_size = $this->requestData['file_size'];
        }
    }

    public function executeGetterExtension($entry)
    {
        return [
            'file_url' => asset("storage/assets/" . $entry->storage_file_name)
        ];
    }
}
