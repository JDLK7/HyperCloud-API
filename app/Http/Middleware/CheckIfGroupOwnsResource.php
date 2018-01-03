<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfGroupOwnsResource
{
    /**
     * Comprueba que el recurso solicitado pertenezca al grupo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $file = $request->folder;

        $fileOwner = $file->group;
        $fileApplicant = $request->group;

        if($fileOwner != $fileApplicant) {
            return response()->json([
                'success' => false,
                'message' => 'El recurso no pertenece al grupo',
            ], 403);
        }

        return $next($request);
    }
}
