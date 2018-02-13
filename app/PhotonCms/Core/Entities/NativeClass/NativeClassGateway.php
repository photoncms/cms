<?php

namespace Photon\PhotonCms\Core\Entities\NativeClass;

use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassTemplateInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassGatewayInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Decouples repository from data sources.
 */
class NativeClassGateway implements NativeClassGatewayInterface
{

    /**
     * Saves model content to a file.
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @return boolean
     */
    public function persistFromTemplate($content, NativeClassTemplateInterface $template)
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
    public function deleteFromTemplate(NativeClassTemplateInterface $template)
    {
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
    public function deleteClassByName($name, $path)
    {
        $fileNameAndPath = "{$path}/{$name}.php";

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
     * @param NativeClassTemplateInterface $classTemplate
     * @return string
     */
    protected function prepareFilenameAndPath(NativeClassTemplateInterface $classTemplate)
    {
        $fileName = $this->prepareFileName($classTemplate);

        if (!$classTemplate->usesPath()) {
            throw new PhotonException('TEMPLATE_PATH_NOT_SET');
        }
        $fileNameAndPath = $classTemplate->getPath() . "/$fileName";

        return $fileNameAndPath;
    }

    /**
     * Compiles filename from a model template
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @return string
     */
    protected function prepareFileName(NativeClassTemplateInterface $classTemplate)
    {
        $fileName = ($classTemplate->usesFilename())
            ? $classTemplate->getFilename()
            : $classTemplate->getClassName() . '.php';

        return $fileName;
    }
}