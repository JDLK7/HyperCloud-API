<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use App\Group;
use App\Folder;
use App\Events\FileCreated;
use App\Events\FileDeleted;
use Illuminate\Http\Request;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

abstract class FileController extends Controller
{   
    /**
     * Servicio...
     *
     * @var \App\Services\FileService
     */
    protected $fileService;

    /**
     * Genera un fichero zip con las rutas de ficheros que recibe por parámetro,
     * manteniendo la posible estructura de directorios de las carpetas. 
     * Devuelve la ruta del propio fichero zip para su descarga.
     *
     * @param array $pathArray
     * @return string
     */
    protected function createZipFile($pathArray) {
        $zip = new \Chumper\Zipper\Zipper;

        $payload = str_random(10);

        $zipPath = public_path(
            "$payload.zip"
        );
        $zip->make($zipPath);
        foreach($pathArray as $p) {
            /**
             * Si es una carpeta se quita la barra de la ruta y se añade
             * al zip para mantener la estructura de directorios.
             */
            if(substr($p, -1) === '/') {
                $folderName = explode('/',$p);
                $folderName = $folderName[count($folderName)-2];
                
                $zip->folder($folderName);
            }
            try {
                $zip->add(Storage::disk('files')->path($p));
            }
            catch(InvalidArgumentException $ex) {}
        }
        $zip->close();

        return $zipPath; 
    }

    /**
     * Lanza el evento de creación de un fichero.
     *
     * @param \App\File $file
     * @return void
     */
    protected function dispatchFileCreatedEvent($file) {
        event(new FileCreated($file));
    }

    /**
     * Lanza el evento de borrado  de un fichero.
     *
     * @param \App\File $file
     * @return void
     */
    protected function dispatchFileDeletedEvent($file) {
        event(new FileDeleted($file));
    }

    /**
     * Envía notificaciones con cambios en ficheros.
     *
     * @param \App\File $file
     * @param string $action
     * @return void
     */
    protected abstract function sendFileNotification($file, $action);

    public function __construct() {
        $this->fileService = new FileService();
    }

    /**
     * Devuelve una respuesta que fuerza al navegador
     * a descargar el fichero solicitado.
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function download(Request $request) {
        $files = $request->get('files');
        $fileCount = count($files);

        if($fileCount === 1) {
            $file = File::find($files[0]);

            if($file->type !== 'folder') {
                return response()->download(
                    Storage::disk('files')->path($file->path),
                    $file->name, 
                    ['X-FileName' => $file->name]
                );
            }
        }

        $pathArray = File::whereIn('id', $files)->get()->pluck('path');
        $zipPath = $this->createZipFile($pathArray);
        $zipName = basename($zipPath);

        if(file_exists($zipPath)) {
            return response()->download(
                $zipPath,
                $zipName,
                ['X-FileName' => $zipName]
            )->deleteFileAfterSend(true);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se ha podido generar el fichero zip',
        ]);
    }

    /**
     * Borra los archivos tanto físicamente como de la BD.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        $files = $request->get('files');

        foreach($files as $id) {
            $file = File::find($id);

            $this->dispatchFileDeletedEvent($file);

            $this->sendFileNotification($file, 'deletion');
            
            $file->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Ficheros borrados correctamente',
        ]);
    }
}
