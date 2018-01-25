<?php

namespace App\Http\Controllers;

use Validator;
use App\Suscription;
use Illuminate\Http\Request;

class SuscriptionController extends Controller
{
    /**
     * Reglas de validación de creación de una suscripción.
     *
     * @var array
     */
    protected $rules = [
        'name'          => 'required|string|max:255',
        'description'   => 'nullable|string|max:255',
        'price'         => 'required|integer',
        'spaceOffer'    => 'required|integer|min:10737418240',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'suscriptions' => Suscription::all(),
        ]);
    }

    /**
     * Crea una nueva suscripción.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, $this->rules);

        if($validator->fails()) {
            $error = $validator->messages();
            
            return response()->json([
                'success'=> false,
                'error'=> $error
            ], 400);
        }

        $suscription = Suscription::create($data);

        return response()->json([
            'success' => true,
            'suscription' => $suscription,
        ], 201);
    }
}
