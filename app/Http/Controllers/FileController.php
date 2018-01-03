<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use App\Group;
use App\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
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
     * @return void
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
}
