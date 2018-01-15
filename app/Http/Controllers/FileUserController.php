<?php

namespace App\Http\Controllers;

use App\User;
use App\Folder;
use Illuminate\Http\Request;
use App\Archive;

class FileUserController extends FileController
{
    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al usuario.
     *
     * @param User $user
     * @return Illuminate\Http\Response
     */
    public function index(User $user) {
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
    public function show(User $user, Folder $folder) {
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
     * contenidos en la carpeta del usuario.
     *
     * @param User $user
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function listFolders(User $user, Folder $folder) {
        $folders = $folder->folders()
            ->where('account_id', $user->account->id);

        return response()->json([
            'success' => true,
            'folders' => $folders,
        ]);
    }

    /**
     * Crea una nueva carpeta y la asocia a la cuenta del usuario.
     *
     * @param Group $group
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function createFolder(Request $request, User $user, Folder $folder) {
        $name = $request->get('name');
        
        if( !$user->account->canStore(4096)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede crear la carpeta porque la cuenta no tiene suficiente espacio disponible',
            ]);
        }

        try {
            $newFolder = $this->fileService->createFolder($name, $folder);
        }
        catch(FileServiceException $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 409);
        }
        
        $newFolder->account()->associate($user->account);
        $newFolder->save();

        return response()->json([
            'success' => true,
            'message' => 'Carpeta creada correctamente',
        ]);
    }

    public function uploadArchive(Request $request, User $user, Folder $folder) {
        $files = $request->file('files');

        foreach($files as $file) {
            $name = $file->getClientOriginalName();
            
            for($i = strlen($name)-1; $i >= 0; $i--){
                if($name[$i] == '.'){
                    $name = substr($name, 0, $i);
                    break;
                }
            }
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();

            try {
                $archive = Archive::create([
                    'name'      => $name,
                    'path'      => "$folder->path$name.$extension",
                    'extension' => $extension,
                    'size'      => $size,
                ]);

                $archive->account()->associate($user->account);
                $archive->folder()->associate($folder);
                $archive->save();

            } catch(Exception $ex) {
                return back()->withErrors($ex->getMessage());
            }

            $file->move(base_path($folder->path), $name.'.'.$extension);
        }

        return back();
    }
}
