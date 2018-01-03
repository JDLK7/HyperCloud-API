<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use App\Group;
use App\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Genera un fichero zip con las rutas de ficheros que recibe por parámetro,
     * manteniendo la posible estructura de directorios de las carpetas. 
     * Devuelve la ruta del propio fichero zip para su descarga.
     *
     * @param array $pathArray
     * @return string
     */
    private function createZipFile($pathArray) {
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
    
    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al usuario.
     *
     * @param User $user
     * @return Illuminate\Http\Response
     */
    public function listUserFiles(User $user) {
        $files = $user->account->files()->paginate(10);

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Devuelve un listado paginado con los ficheros
     * contenidos en la carpeta del usuario.
     *
     * @param User $user
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function listUserFolder(User $user, Folder $folder) {
        $files = $folder->files()
            ->where('account_id', $user->account->id)
            ->paginate(10);

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al grupo.
     *
     * @param Group $group
     * @return Illuminate\Http\Response
     */
    public function listGroupFiles(Group $group) {
        $files = $group->files()->paginate(10);

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Devuelve un listado paginado con los ficheros
     * contenidos en la carpeta del grupo.
     *
     * @param Group $group
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function listGroupFolder(Group $group, Folder $folder) {
        $files = $folder->files()
            ->where('group_id', $group->id)
            ->paginate(10);

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
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
            $filePath = base_path(File::find($files[0])->path);
            
            return response()->download($filePath);
        }

        $pathArray = File::whereIn('id', $files)->get()->pluck('path');
        $zipPath = $this->createZipFile($pathArray);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
