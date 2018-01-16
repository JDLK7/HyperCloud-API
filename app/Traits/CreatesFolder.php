<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Trait que crea la carpeta física cuando el modelo es creado.
 */
trait CreatesFolder
{
    /**
     * Boot function from laravel.
     */
    protected static function bootCreatesFolder()
    {
        static::created(function ($model) {
            if (isset($model->path)) {
                Storage::disk('files')->makeDirectory($model->path);
            }
        });
    }
}