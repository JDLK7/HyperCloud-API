<?php

namespace App\Services;

use App\Folder;
use App\Archive;

class FileService 
{
    /**
     * Crea una carpeta una nueva carpeta dentro de la carpeta $parent.
     *
     * @param string $name
     * @param \App\Folder $parent
     * @return \App\Folder
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

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $extension
     * @param int $size
     * @param \App\Folder $parent
     * @return \App\Archive
     */
    public function createArchive($name, $extension, $size, Folder $parent) {
        $path = $parent->path . $name;

        $exists = Archive::where('path', $path)->exists();

        if($exists) {
            throw new FileServiceException('No se ha podido subir el archivo porque ya existe');
        }

        $archive = Archive::create([
            'name'      => $name,
            'path'      => $path,
            'extension' => $extension,
            'size'      => $size,
        ]);

        $archive->folder()->associate($parent);
        $archive->save();

        return $archive;
    }
}