<?php

namespace App;

class Archive extends File
{
    /**
     * Nombre del tipo que se guardará en la columna "type".
     *
     * @var string
     */
    protected static $singleTableType = 'archive';
}
