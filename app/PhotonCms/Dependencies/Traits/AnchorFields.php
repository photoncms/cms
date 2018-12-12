<?php
 
namespace Photon\PhotonCms\Dependencies\Traits;
use \Carbon\Carbon;

trait AnchorFields
{

    /**
     * Retrieves application's base url
    *
    * @return string
    */
    public function baseUrl($item, $arguments = [])
    {
        return url("/");
    }

    /**
     * Check if news is approved and return appropriate html class
     *
     * @param object $item
     * @param array $arguments
     * @return string
     */
    public function isApproved($item, $arguments = [])
    {
        if(!$item->approved) {
            return "ua";
        }

        return "";
    }

    /**
     * Fetch location of thumbnail imgage
    *
    * @param object $item
    * @param array $arguments
    * @return string
    */
    public function thumbnail($item, $arguments = [])
    {
        return $item->headline_image_relation->resized_images_relation[0]->storage_file_name . "?" . time();
    }

    /**
     * Fetch formated publish date
    *
    * @param object $item
    * @param array $arguments
    * @return string
    */
    public function getFormatedPublishDate($item, $arguments = [])
    {
        return Carbon::parse($item->publish_date)->format("d.m.Y H:i");
    }

    /**
     * Fetch formated created_at date
    *
    * @param object $item
    * @param array $arguments
    * @return string
    */
    public function getFormatedCreatedAtDate($item, $arguments = [])
    {
        return Carbon::parse($item->created_at)->format("d.m.Y - H:i");
    }
}