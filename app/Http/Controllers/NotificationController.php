<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Lista las notificaciones sin leer.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $user = $request->user();

        $notifications = $user->unreadNotifications->groupBy('type');

        return response()->json([
            'success'       => true,
            'notifications' => [
                'user'  => $notifications->first(),
                'group' => $notifications->last(),
            ],
        ]);
    }

    public function update(Request $request, $notification) {
        $user = $request->user();

        $n = $user->unreadNotifications()->where('id', $notification)->first();
        
        if(is_null($n)) {
            return response()->json([
                'success' => false,
                'message' => 'La notificación no existe',
            ], 404);
        }

        $n->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída',
        ], 204);
    } 
}
