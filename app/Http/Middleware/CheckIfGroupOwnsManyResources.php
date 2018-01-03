<?php

namespace App\Http\Middleware;

use Closure;

use App\Group;

class CheckIfGroupOwnsManyResources
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
        $groupId = $request->group;
        $files = $request->get('files');
        $group = Group::find($groupId);

        $ownedCount = $group->files()->whereIn('id', $files)->count();
        $requestedCount = count($files);

        if($ownedCount != $requestedCount) {
            return response()->json([
                'success' => false,
                'message' => 'Alguno de los recursos solicitados no pertenece al grupo',
            ], 403);
        }

        return $next($request);
    }
}
