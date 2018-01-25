<?php

namespace App\Http\Controllers;

use App\User;
use App\File;
use App\Folder;
use App\Archive;
use Illuminate\Http\Request;
use App\Notifications\UserFileChange;
use App\Services\FileServiceException;
use Illuminate\Support\Facades\Storage;

class FileUserController extends FileController
{
    /**
     * Envía una notificación al usuario con los cambios del fichero.
     * 
     * @param \App\File $file
     * @param string $action
     * @return void
     */
    protected function sendFileNotification($file, $action) {
        if( !is_null($file->account)) {
            $file->account->user->notify(new UserFileChange($file, $action));
        }
    }

    /**
     * Devuelve un listado paginado con los ficheros 
     * pertenecientes al usuario.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user) {
        $files = $user->account->files()
            ->orderBy('type', 'desc')
            ->orderBy('name')
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
     * @param \App\User $user
     * @param \App\Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Folder $folder) {
        $files = $folder->files()
            ->where('account_id', $user->account->id)
            ->orderBy('type', 'desc')
            ->orderBy('name')
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
     * @param \App\User $user
     * @param \App\Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function listFolders(User $user, Folder $folder) {
        $folders = $folder->folders()
            ->where('account_id', $user->account->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'folders' => $folders,
        ]);
    }

    /**
     * Crea una nueva carpeta y la asocia a la cuenta del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @param \App\Folder $folder
     * @return \Illuminate\Http\Response
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

        $this->dispatchFileCreatedEvent($newFolder);
        $this->sendFileNotification($newFolder, 'creation');

        return response()->json([
            'success' => true,
            'message' => 'Carpeta creada correctamente',
            'folder' => $newFolder,
        ]);
    }

    /**
     * Sube un fichero y lo asocia a la cuenta del usuario
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @param \App\Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function uploadArchive(Request $request, User $user, Folder $folder) {
        $files = $request->file('files');

        foreach($files as $file) {
            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();

            if( !$user->account->canStore($size)) {
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

            $newArchive->account()->associate($user->account);
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

    /**
     * Comparte públicamente los ficheros y devuelve
     * un enlace único para acceder a ellos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request, User $user) {
        $this->validate($request, [
            'files' => 'required|array'
        ]);

        $files = $request->get('files');

        $shareableLink = str_random(40);

        File::whereIn('id', $files)->update([
            'shareable_link' => $shareableLink,
        ]);

        return response()->json([
            'success' => true,
            'shareable_link' => $shareableLink,
        ]);
    }
}
