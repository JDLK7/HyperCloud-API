<?php

namespace App;

use App\Traits\CreatesFolder;

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
}
