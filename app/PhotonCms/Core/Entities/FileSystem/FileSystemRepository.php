<?php

namespace Photon\PhotonCms\Core\Entities\FileSystem;

use Symfony\Component\HttpFoundation\File\File;

class FileSystemRepository
{
    /**
     * Saves a file to the specified disk.
     * Directory can also be specified, if not the file will go to the root of the disk.
     * If overwrite flag is set, the file with an ambigous file name will overwrite an existing file.
     * If the file name is set, this will be the new file name. This value will also cooperate with the
     * overwrite flag. The file name should incoorporate an extenstion too.
     *
     * @param File $file
     * @param string $disk
     * @param string $directory
     * @param boolean $overwriteIfExists
     * @param string $fileName
     * @return boolean|string
     */
    public function save(File $file, $disk, $directory = null, $overwriteIfExists = false, $fileName = null)
    {
        // Prepare the file name and extension
        $extension = '.'.$file->getClientOriginalExtension();
        if (!$fileName) {
            $fileName = $file->getClientOriginalName();
            $fileName = substr($fileName, 0, strlen($fileName) - strlen($extension));
        }
        
        $filenameWithExtension = $fileName.$extension;

        // Prepare the directory and full filename
        $directory = $directory ? $directory : "";
        if (strlen($directory) > 0 && substr($directory, -1) !== '/') {
            $directory .= '/';
        }
        $fileNameAndPath = $directory.$filenameWithExtension;

        // Check existing file names and if the new one matches an existing one, concatenate a suffix to the new name
        if (!$overwriteIfExists) {
            $increment = 1;
            while(\Storage::disk($disk)->exists($fileNameAndPath)) {
                $filenameWithExtension = $fileName.$increment.$extension;
                $fileNameAndPath = $directory.$filenameWithExtension;
                $increment++;
            }
        }

        // Store the file
        if (\Storage::disk($disk)->put($fileNameAndPath, \File::get($file))) {
            \Storage::disk($disk)->setVisibility($fileNameAndPath, 'public');
            return $fileNameAndPath;
        }
        
        return false;
    }
}