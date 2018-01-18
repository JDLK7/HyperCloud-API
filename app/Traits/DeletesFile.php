<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Trait que crea la carpeta física cuando el modelo es creado.
 */
trait DeletesFile
{
    /**
     * Boot function from laravel.
     */
    protected static function bootDeletesFile()
    {
        static::deleting(function ($model) {
            if (isset($model->path)) {
                if($model->isFolder()) {
                    Storage::disk('files')->deleteDirectory($model->path);
                }
                else {
                    Storage::disk('files')->delete($model->path);
                }
            }
        });
    }
}