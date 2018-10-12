<?php

namespace Photon\PhotonCms\Core\Jobs;

use Photon\PhotonCms\Core\Entities\Rekognition\Rekognition;
use Photon\PhotonCms\Core\IAPI\IAPI;
use Photon\PhotonCms\Dependencies\DynamicModels\FileTags;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\NotificationHelperFactory;

class TagCelebrities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * \Photon\PhotonCms\Dependencies\DynamicModels\Assets object
     *
     * @var  [\Photon\PhotonCms\Dependencies\DynamicModels\Assets]
     */
    private $item;

    /**
     * Options used by the RekognitionClient
     *
     * @var  [Array]
     */
    private $options;

    /**
     * Rekognition Service
     *
     * @var  Rekognition
     */
    private $rekognition;

    /**
     * IAPI
     *
     * @var  IAPI
     */
    private $iapi;

    /**
     * Class constructor
     *
     * @param  \Photon\PhotonCms\Dependencies\DynamicModels\Assets  $item
     */
    public function __construct(\Photon\PhotonCms\Dependencies\DynamicModels\Assets $item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job
     *
     * @param  Rekognition  $rekognition
     * @param  IAPI  $iapi
     * @return  void
     */
    public function handle(
        Rekognition $rekognition,
        IAPI $iapi
    )
    {
        $this->rekognition = $rekognition;

        $this->iapi = $iapi;

        if (!($this->item->image_width > 0 && $this->item->image_height > 0)) {
            return;
        }

        $path = config('filesystems.disks.assets.root') . '/' . $this->item->storage_file_name;

        $celebrities = $this->rekognition->recognizeCelebrities($path);

        if(!isset($celebrities['CelebrityFaces'])) {
            throw new PhotonException('REKOGNITION_SERVICE_RETURNED_UNEXPECTED_DATA');
        }

        $tags = $this->item->tags_relation->map(function($tag) {
            return $tag->id;
        })->toArray();

        $recognizedFaces = [];

        foreach($celebrities['CelebrityFaces'] as $celebrityFace) {
            $tag = $this->getOrCreateTagId($celebrityFace['Name'], $iapi);

            if (!in_array($tag, $tags)) {
                array_push($tags, $tag);

                $recognizedFaces[] = $celebrityFace['Name'] . ' (' . round($celebrityFace['Face']['Confidence'], 2) . '%)';
            }
        }

        $asset = $this->iapi->assets($this->item->id)->put(compact('tags'));

        NotificationHelperFactory::makeByHelperName("CelebritiesTagged")
            ->notify(compact('asset', 'recognizedFaces'));
    }

    /**
     * Gets or creates file tag id
     *
     * @param   string  $title
     * @return  integer
     */
    private function getOrCreateTagId($title)
    {
        $tag = FileTags::where('title', $title)->first();

        if($tag) {
            return $tag->id;
        }

        $newTag = $this->iapi->file_tags->post([
                'title' => $title,
            ]);

        return $newTag->id;
    }
}
