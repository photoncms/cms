<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Decouples repository from data sources.
 */
class ModelGateway implements ModelGatewayInterface
{

    /**
     * Saves model content to a file.
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @return boolean
     */
    public function persistFromTemplate($content, ModelTemplateInterface $template)
    {
        $fileNameAndPath = $this->prepareFilenameAndPath($template);

        return file_put_contents($fileNameAndPath, $content);
    }

    /**
     * Deletes a model file using a ModelTemplate instance
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @return boolean
     */
    public function deleteFromTemplate(ModelTemplateInterface $template) {
        $fileNameAndPath = $this->prepareFilenameAndPath($template);

        return $this->deleteFile($fileNameAndPath);
    }

    /**
     * Deletes a model file by its model name.
     * This assumes that the model file was created under identical model name.
     *
     * @param string $name
     * @param string $path
     * @return boolean
     */
    public function deleteClassByName($name)
    {
        $fileNameAndPath = app_path(config('photon.dynamic_models_location'))."/{$name}.php";

        return $this->deleteFile($fileNameAndPath);
    }

    /**
     * Deletes a file on a path.
     *
     * @param string $fileNameAndPath
     * @return boolean
     */
    private function deleteFile($fileNameAndPath)
    {
        if (file_exists($fileNameAndPath)) {
            unlink($fileNameAndPath);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Compiles filename and path from the ModelTemplate.
     *
     * @param ModelTemplateInterface $modelTemplate
     * @return string
     */
    protected function prepareFilenameAndPath(ModelTemplateInterface $modelTemplate)
    {
        if (!$modelTemplate instanceof ModelTemplateInterface) {
            throw new PhotonException('ILLEGAL_CLASS_INSTANCE', ['expected' => 'Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface']);
        }

        $fileName = $this->prepareFileName($modelTemplate);

        $fileNameAndPath = ($modelTemplate->usesPath())
            ? $modelTemplate->getPath() . "/$fileName"
            : app_path(config('photon.dynamic_models_location') . "/$fileName");

        return $fileNameAndPath;
    }

    protected function prepareFileName(ModelTemplateInterface $classTemplate)
    {
        $fileName = ($classTemplate->usesFilename())
            ? $classTemplate->getFilename()
            : $classTemplate->getClassName() . '.php';

        return $fileName;
    }
}