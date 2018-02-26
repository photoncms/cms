<?php

namespace Photon\PhotonCms\Core\Commands;

use Illuminate\Console\Command;
use Photon\PhotonCms\Core\IAPI\IAPI;
use Carbon\Carbon;

use Photon\PhotonCms\Dependencies\DynamicModels\Assets;
use Photon\PhotonCms\Dependencies\DynamicModels\ResizedImages;
use Photon\PhotonCms\Dependencies\DynamicModels\ImageSizes;
use Photon\PhotonCms\Core\Entities\Image\ImageRepository;
use Photon\PhotonCms\Core\Entities\Image\ImageGatewayFactory;
use Photon\PhotonCms\Core\Entities\Image\ImageFactoryFactory;

class RebuildResizedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photon:rebuild-resized-images {sizeId} {--showLog} {--force}';

    protected $IAPI;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuilds all images of given image size';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->IAPI = new \Photon\PhotonCms\Core\IAPI\IAPI();
        $this->imageRepository = $imageRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $showLog = (bool) $this->option('showLog');
        $force = (bool) $this->option('force');
        $sizeId = $this->argument('sizeId');
        $imageSize = ImageSizes::find($sizeId);

        if(!$imageSize) {
            $this->error("Image size not found");
            return false;
        }

        $bar = $this->output->createProgressBar(Assets::all()->count());

        $offset = 0;
        $limit = 10000;

        while (true) {
            $assets = Assets::with(["resized_images_relation" => function ($query) use ($imageSize) {
                $query->where('image_size', $imageSize->id);
            }])->skip($offset * $limit)->take($limit)->get();

            if(count($assets) == 0)
                break;

            foreach ($assets as $key => $asset) {

                $bar->advance();

                if(count($asset->resized_images_relation) == 1 && \Storage::disk("assets")->exists($asset->resized_images_relation[0]->storage_file_name) && !$force) {
                    $showLog ? $this->info("Resized image for asset ID:" . $asset->id . " and size ID: " . $sizeId . " exists.") : "";
                    continue;
                }

                foreach ($asset->resized_images_relation as $key => $existinResizedImage) {
                    $this->IAPI->resized_images($existinResizedImage->id)->delete(['force' => true]);
                }

                $showLog ? $this->info("Resized image for asset ID:" . $asset->id . " and size ID: " . $sizeId . " regenerate") : "";

                $this->regenerateSingleSize($asset, $imageSize);
            }

            $offset++;
        }

        $bar->finish();

    }

    private function regenerateSingleSize($item, $imageSize)
    {
        // Prepare tools and resources
        $imageFactory = ImageFactoryFactory::make(env('IMAGE_SOFTWARE'));

        $originalImageFileName = $item->storage_file_name;
        $path = config('filesystems.disks.assets.root') . '/' . $item->storage_file_name;
        $image = $imageFactory::makeFromFile($path);
        if(!$image)
            return true;

        $imageGateway = ImageGatewayFactory::make(env('IMAGE_SOFTWARE'), $path);

        if(!$image) {
            $this->error("Main asset not found");
            return false;
        }

        $inWidth = $this->imageRepository->getImageWidth($image, $imageGateway);
        $inHeight = $this->imageRepository->getImageHeight($image, $imageGateway);

        $outWidth = ($imageSize->width == 0) // 0 means auto
            ? $inWidth
            : $imageSize->width;
        $outHeight = ($imageSize->height == 0) // 0 means auto
            ? $inHeight
            : $imageSize->height;

        $this->generateResizedImage($inWidth, $inHeight, $outWidth, $outHeight, $image, $originalImageFileName, 1, $imageGateway, $imageSize, $item);
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
}
