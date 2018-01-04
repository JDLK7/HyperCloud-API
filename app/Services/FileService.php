<?php

namespace App\Services;

use App\Folder;

class FileService 
{
    /**
     * Crea una carpeta una nueva carpeta dentro de la carpeta $parent.
     *
     * @param string $name
     * @param Folder $parent
     * @return Folder
     */
    public function createFolder($name, Folder $parent) {
        $path = $parent->path . $name . "/";

        $exists = Folder::where('path', $path)->exists();

        if($exists) {
            throw new FileServiceException('No se ha podido crear la carpeta porque ya existe');
        }
        
        $folder = Folder::create([
            'name' => $name,
            'path' => $path,
            'size' => 4096,
        ]);

        $folder->folder()->associate($parent);
        $folder->save();

        return $folder;
    }
}