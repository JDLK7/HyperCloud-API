<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use App\Group;
use App\Folder;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $zip = new \Chumper\Zipper\Zipper;
        $zipPath = public_path(
            hash('crc32', $user->email.time()).
            '.zip'
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
                $zip->add(base_path($p));
            }
            catch(InvalidArgumentException $ex) {}
        }
        $zip->close();

        return $zipPath; 
    }

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
                return response()->download(base_path($file->path));
            }
        }

        $pathArray = File::whereIn('id', $files)->get()->pluck('path');
        $zipPath = $this->createZipFile($pathArray);

        if(file_exists($zipPath)) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No se ha podido generar el fichero zip',
        ]);
    }
}
