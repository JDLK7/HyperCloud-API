<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class SharedFileController extends Controller
{
    /**
     * A partir de un enlace único devuelve la información 
     * de los posibles ficheros públicos asociados.
     *
     * @param string $shareableLink
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($shareableLink) {
        $files = File::where('shareable_link', $shareableLink)->get();

        if($files->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No existen ficheros asociados al enlace',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    /**
     * Realiza la descarga de ficheros públicos.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $shareableLink
     * @return \Symphony\Component\HttpFoundationBinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Request $request, $shareableLink) {
        $this->validate($request, [
            'files' => 'required|array',
        ]);

        $files = $request->get('files');

        $areDownloadable = (File::whereIn('id', $files)
            ->whereNull('shareable_link')
            ->orWhere('shareable_link', '<>', $shareableLink)
            ->count() === 0);

        if(!$areDownloadable) {
            return response()->json([
                'success' => false,
                'message' => 'Algún archivo no corresponde con el enlace proporcionado',
            ], 403);
        }

        return (new FileUserController())->download($request);
    }
}
