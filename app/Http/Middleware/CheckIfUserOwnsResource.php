<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfUserOwnsResource
{
    /**
     * Comprueba si el recurso solicitado pertenece
     * al usuario que lo solicita.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $file = $request->route('folder');

        $fileOwner = $file->account;
        $fileApplicant = $user->account;

        if($fileOwner != $fileApplicant) {
            return response()->json([
                'success' => false,
                'message' => 'El recurso no le pertenece',
            ], 403);
        }

        return $next($request);
    }
}
