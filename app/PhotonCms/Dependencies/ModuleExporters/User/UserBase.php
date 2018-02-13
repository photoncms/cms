<?php

namespace Photon\PhotonCms\Dependencies\ModuleExporters\User;

use Photon\PhotonCms\Core\Entities\DynamicModuleExporter\DynamicModuleExporterBase;

use Photon\PhotonCms\Core\Entities\DynamicModuleExporter\Contracts\DynamicModuleExporterMultipleInterface;

class UserBase extends DynamicModuleExporterBase implements DynamicModuleExporterMultipleInterface
{

    protected function makeExporter($entry, $filename, $parameters = [])
    {
        return \Excel::create($filename, function($excel) use ($entry) {
            // Ready for implementation
        });
    }

    protected function makeMultipleExporter($entries, $filename, $parameters = [])
    {
        return \Excel::create($filename, function($excel) use ($entries) {
            $loggedInUser = \Auth::user();

            $excel->setTitle('Users');

            $excel->setCreator("{$loggedInUser->first_name} {$loggedInUser->last_name}");


            $excel->sheet('Users', function($sheet) use ($entries) {

                $sheet->appendRow(array(
                    'First name', 'Last name', 'email', 'Confirmed', 'Roles'
                ));
                $sheet->row($sheet->getHighestRow(), function($row) {
                    $row->setBackground('#CCCCCC');
                });

                foreach ($entries as $entry) {
                    $roles = $entry->roles_relation;
                    $roleNames = '';
                    if ($roles) {
                        foreach ($roles as $role) {
                            $roleNames .= " $role->title";
                        }
                    }

                    $sheet->appendRow(array(
                        $entry->first_name,
                        $entry->last_name,
                        $entry->email,
                        (($entry->confirmed) ? 'Yes' : 'No'),
                        $roleNames
                    ));
                }

            });
        });
    }
}