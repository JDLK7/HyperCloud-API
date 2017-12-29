<?php

namespace App;

class Folder extends File
{
    /**
     * Nombre del tipo que se guardará en la columna "type".
     *
     * @var string
     */
    protected static $singleTableType = 'folder';
}
