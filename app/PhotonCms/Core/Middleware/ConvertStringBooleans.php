<?php

namespace Photon\PhotonCms\Core\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertStringBooleans
{

    public function handle($request, Closure $next)
    {
        // Should convert only if Content-type is multipart/form-data,
        // as multipart/form-data can't send anyhing else but string
        if(strpos($request->headers->get('Content-Type'), 'multipart/form-data') === false) {
            return $next($request);
        }

        $data = $request->all();

        $data = $this->convertStringBooleans($data);

        $request->replace($data);

        return $next($request);
    }

    /**
     * Converts all string booleans to booleans
     *
     * @param   array  $data
     * @return  array
     */
    private function convertStringBooleans($data) {
        foreach($data as $key=>$value) {
            if($value === 'true' || $value === 'TRUE')
                $data[$key] = true;

            if($value === 'false' || $value === 'FALSE')
                $data[$key] = false;

            if (is_array($value)) {
                $data[$key] = $this->convertStringBooleans($value);
            }
        }

        return $data;
    }
}
