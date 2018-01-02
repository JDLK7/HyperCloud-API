<?php

namespace App\Traits;

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
            if (isset($model->path) && !file_exists(base_path($model->path))) {
                mkdir(base_path($model->path), 0755, true);
            }
        });
    }
}