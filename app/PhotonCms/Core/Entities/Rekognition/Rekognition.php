<?php

namespace Photon\PhotonCms\Core\Entities\Rekognition;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\File;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class Rekognition
{
    /**
     * Options used by the RekognitionClient
     *
     * @var array
     */
    private $options;

    /**
     * RekognitionClient instance
     *
     * @var RekognitionClient
     */
    private $rekognition;

    /**
     * Create a new Rekognition Client
     */
    public function __construct()
    {
        $this->options = [
            'region' => env('AWS_REGION'),
            'version' => 'latest',
        ];

        $this->rekognition = new RekognitionClient($this->options);
    }

    /**
     * Retrieves the laravel type of the attribute used in laravel migrations.
     *
     * @var string  $imagePath
     * @return string
     */
    public function recognizeCelebrities($imagePath)
    {
        $image = File::get($imagePath);

        try {
            return $this->rekognition->recognizeCelebrities([
                'Image' => [
                    'Bytes' => $image,
                ],
            ]);
        } catch (Exception $e) {
            Log::error("Couldn't fetch the rekognition data from AWS.", $e);

            throw new PhotonException('FAILED_TO_CONNECT_WITH_REKOGNITION_SERVICE');
        }
    }
}
