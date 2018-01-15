<?php

namespace App\Services;

use Exception;

class FileServiceException extends Exception
{
    /**
     * Crea una nueva excepción del servicio FileService.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message = 'No se ha podido crear el recurso.')
    {
        parent::__construct($message);
    }
}
