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
}
