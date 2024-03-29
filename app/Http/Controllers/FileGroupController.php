<?php

namespace App\Http\Controllers;

use App\Group;
use App\Folder;
use Illuminate\Http\Request;
use App\Services\FileServiceException;
use App\Notifications\GroupFileChange;
use Illuminate\Support\Facades\Storage;

class FileGroupController extends FileController
{
    /**
     * Envía una notificación a todas las cuentas 
     * del grupo con los cambios en el fichero.
     *
     * @param \App\File $file
     * @param string $action
     * @return void
     */
    protected function sendFileNotification($file, $action) {
        if( !is_null($file->group)) {
            foreach($file->group->accounts as $account) {
                $account->user->notify(new GroupFileChange($file, $action));
            }
        }
    }

    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al grupo.
     *
     * @param \App\Group $group
     * @return Illuminate\Http\Response
     */
    public function index(Group $group) {
        $files = $group->files()
            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Devuelve un listado paginado con los ficheros
     * contenidos en la carpeta del grupo.
     *
     * @param \App\Group $group
     * @param \App\Folder $folder
     * @return \App\Illuminate\Http\Response
     */
    public function show(Group $group, Folder $folder) {
        $files = $folder->files()
            ->where('group_id', $group->id)
            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Crea una nueva carpeta y la asocia al grupo.
     *
     * @param \App\Group $group
     * @param \App\Folder $folder
     * @return \App\Illuminate\Http\Response
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

        $this->dispatchFileCreatedEvent($newFolder);
        $this->sendFileNotification($newFolder, 'creation');

        return response()->json([
            'success' => true,
            'message' => 'Carpeta creada correctamente',
            'folder' => $newFolder,
        ]);
    }

    /**
     * Sube un fichero y lo al grupo
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Group $group
     * @param \App\Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function uploadArchive(Request $request, Group $group, Folder $folder) {
        $files = $request->file('files');

        foreach($files as $file) {
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();

            if( !$group->canStore($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede subir el archivo porque la cuenta no tiene suficiente espacio disponible',
                ]);
            }

            try {
                $newArchive = $this->fileService->createArchive($name, $extension, $size, $folder);

            } catch(FileServiceException $ex) {
                return response()->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                ], 409);
            }

            $newArchive->group()->associate($user->account);
            $newArchive->save();

            $this->dispatchFileCreatedEvent($newArchive);
            $this->sendFileNotification($newArchive, 'creation');

            $file->storeAs($folder->path, $name, 'files');
        }

        return response()->json([
            'success' => true,
            'message' => 'Archivo/s subido correctamente',
            'archive' => $newArchive,
        ]);
    }
}
