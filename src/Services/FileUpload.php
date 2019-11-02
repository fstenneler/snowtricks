<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;


class FileUpload
{

    /**
     * Upload form submitted file
     *
     * @param UploadFile $file
     * @param string $destinationPath
     * @return Array
     */
    public function upload($file, $destinationPath)
    {

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
               
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Move the file to the directory
        try {
            $file->move(
                $destinationPath,
                $newFilename
            );
        } catch (FileException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }

        return ['success' => true, 'fileName' => $newFilename];

    }

}
