<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassTemplate;
use Illuminate\Support\Facades\Artisan;

class MigrationGateway implements MigrationGatewayInterface
{
    /**
     * Persists the model migration class content into a migration file using the information from the model migration template.
     *
     * @param type $content
     * @param NativeClassTemplate $template
     * @throws BaseException
     */
    public function persistFromTemplate($content, NativeClassTemplate $template)
    {
        $fileNameAndPath = base_path($template->getPath().'/'.$template->getFileName());

        $bytesWritten = \File::put($fileNameAndPath, $content);
        if ($bytesWritten === false)
        {
            throw new BaseException('FILE_WRITING_FAILURE');
        }

        return true;
    }

    public function runFromTemplate(NativeClassTemplate $template)
    {
        $path = ($template->usesPath())
            ? $template->getPath()
            : config('photon.dynamic_model_migrations_dir');

        return Artisan::call('migrate', ['--path' => $path, '--force' => true]);
    }

    public function deleteFromTemplate(NativeClassTemplate $template)
    {
        $filePathAndName = base_path($template->getPath().'/'.$template->getFileName());
        if (file_exists($filePathAndName)) {
            unlink($filePathAndName);
            return true;
        }
        return false;
    }
}