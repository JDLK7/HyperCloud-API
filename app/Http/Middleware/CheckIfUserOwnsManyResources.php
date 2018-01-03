<?php

namespace App\Http\Middleware;

use Closure;

use App\File;

class CheckIfUserOwnsManyResources
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $files = $request->get('files');

        $ownedCount = $user->account->files()->whereIn('id', $files)->count();
        $requestedCount = count($files);

        if($ownedCount != $requestedCount) {
            return response()->json([
                'success' => false,
                'message' => 'Alguno de los recursos solicitados no le pertenece',
            ], 403);
        }

        return $next($request);
    }
}
