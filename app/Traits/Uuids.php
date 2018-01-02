<?php

namespace App\Traits;

use Webpatser\Uuid\Uuid;

trait Uuids
{
    /**
     * Se registra el hook con la generación del UUID en el método boot 
     * del Trait, que se ejecutará como si fuera la función boot
     * de un Model.
     */
    protected static function bootUuids()
    {
        static::creating(function ($model) {
            $model->{ $model->getKeyName() } = Uuid::generate()->string;
        });
    }
}