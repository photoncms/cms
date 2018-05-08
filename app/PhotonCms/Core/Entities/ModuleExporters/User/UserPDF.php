<?php

namespace Photon\PhotonCms\Core\Entities\ModuleExporters\User;

class UserPDF extends UserBase
{

    protected function makeMultipleExporter($entries, $filename, $parameters = [])
    {
        
        $data = [
            'users' => $entries
        ];
        $html = $this->compileFromExporterTemplate('users', $data);

        $pdf = \PDF::loadHTML($html);
        return $pdf;
    }
}