<?php

namespace App\Http\Middleware;

use Closure;

use App\Group;

class CheckIfUserIsGroupMember
{
    /**
     * Comprueba si el usuario que ha realizado la petición 
     * de un recurso del grupo pertenece al mismo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $group = $request->route('group');

        $isMember = $group->accounts()->find($user->account->id) !== null;

        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'No perteneces al grupo.'
            ], 403);
        }

        return $next($request);
    }
}
