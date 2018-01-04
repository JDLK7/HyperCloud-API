<?php

namespace App\Http\Controllers;

use App\User;
use App\Folder;
use Illuminate\Http\Request;

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
     * Crea una nueva carpeta y la asocia a la cuenta del usuario.
     *
     * @param Group $group
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function createFolder(Request $request, User $user, Folder $folder) {
        $name = $request->get('name');

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
}
