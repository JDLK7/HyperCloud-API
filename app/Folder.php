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
}
