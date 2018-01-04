<?php

namespace App\Http\Controllers;

use App\Group;
use App\Folder;
use App\Services\FileServiceException;

use Illuminate\Http\Request;

class FileGroupController extends FileController
{
    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al grupo.
     *
     * @param Group $group
     * @return Illuminate\Http\Response
     */
    public function index(Group $group) {
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
    public function show(Group $group, Folder $folder) {
        $files = $folder->files()
            ->where('group_id', $group->id)
            ->paginate(10);

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Crea una nueva carpeta y la asocia al grupo.
     *
     * @param Group $group
     * @param Folder $folder
     * @return Illuminate\Http\Response
     */
    public function createFolder(Request $request, Group $group, Folder $folder) {
        $name = $request->get('name');
        
        if( !$group->canStore(4096)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede crear la carpeta porque alguna cuenta no tiene suficiente espacio disponible',
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
        
        $newFolder->group()->associate($group);
        $newFolder->save();

        return response()->json([
            'success' => true,
            'message' => 'Carpeta creada correctamente',
        ]);
    }
}
