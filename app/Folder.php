<?php

namespace App;

use App\Traits\CreatesFolder;
use Illuminate\Support\Facades\DB;

class Folder extends File
{
    
    use CreatesFolder;

    /**
     * Nombre del tipo que se guardará en la columna "type".
     *
     * @var string
     */
    protected static $singleTableType = 'folder';

    /**
     * Relación reflexiva que representa los ficheros contenidos en una Carpeta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files() {
        return $this->hasMany('App\File');
    }

    /**
     * Relación reflexiva que representa las carpetas 
     * contenidas dentro de otra carpeta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders() {
        return $this->hasMany('App\Folder');
    }

    /**
     * Relación reflexiva que representa los archivos 
     * contenidas dentro de una carpeta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archives() {
        return $this->hasMany('App\Archive');
    }

    /**
     * Renombra la carpeta y cambia el path de todos los ficheros descendientes.
     *
     * @param string $name
     * @return boolean
     */
    public function rename($name) {
        $newPath = str_replace_last($this->name, $name, $this->path);

        DB::table('files')->where('path', 'like', "{$this->path}%")
            ->update([
                'path' => DB::raw("REPLACE(path, '{$this->path}', '{$newPath}')")
            ]);

        return parent::rename($name);
    }
}
