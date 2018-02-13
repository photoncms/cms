<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Dependencies\DynamicModels\User;

class LicenseKeyHelper
{

	/**
	* Check if license exist in storage folder
	*/
	public static function checkLicenseKey()
	{
		try {
		    $content = \File::get(storage_path("license.key"));
		} catch (\Exception $exception) {
		    return null;
		}

		return $content;
	}


	/**
	* Check license on photon license home
	*/
	public static function pingHome($key)
	{
		$client = new \GuzzleHttp\Client();
		$userCount = User::count();
		try {
			$res = $client->request('POST', 'https://haven.photoncms.com/license-call-home', [
	    		'headers' => [
	    			'Accept' => 'application/json'
	    		],
	            'form_params' => ['license_key' => $key, 'domain_name' => url("/"), 'user_count' => $userCount],
	            'timeout' => 30,
        		'connect_timeout' => 30
	        ]);
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			$response = $e->getResponse();
			if(is_null($response))
            	throw new PhotonException('PHOTON_LICENSE_KEY_VALIDATION_FAILED');

		    $responseBody = json_decode($response->getBody()->getContents(), true); 

			if(is_null($responseBody))
            	throw new PhotonException('PHOTON_LICENSE_KEY_VALIDATION_FAILED');
            
		    if(isset($responseBody['body']['disabled']) && !$responseBody['body']['disabled'])
        		Cache::put('photon-license', $responseBody, 60);
        	else 
			    Cache::pull('photon-license');

            throw new PhotonException($responseBody['message'], $responseBody['body']);
		}
		
		return json_decode($res->getBody()->getContents(), true); 
	}

	public static function storeLiceseKey($key)
	{
		\File::put(storage_path("license.key"), $key);

		return true;
	}
}