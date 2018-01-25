<?php

namespace App\Http\Controllers;

use App\User;
use App\Account;
use App\Archive;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Devuelve un listado con métricas útiles para un administrador.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function metrics(Request $request) {
        $registeredUsers = User::count();
        $uploadedFiles = Archive::count();
        $usedSpace = intval(Account::sum('space'));

        return response()->json([
            'success' => true,
            'metrics' => [
                'registeredUsers'   => $registeredUsers,
                'uploadedFiles'     => $uploadedFiles,
                'usedSpace'         => $usedSpace,
            ],
        ]);
    }
}
