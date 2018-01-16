<?php

namespace App\Http\Controllers;

use App\Suscription;
use Illuminate\Http\Request;

class SuscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'suscriptions' => Suscription::all(),
        ]);
    }
}
