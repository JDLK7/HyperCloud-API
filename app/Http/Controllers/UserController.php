<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Suscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Datos editables del modelo User.
     *
     * @var array
     */
    protected $userEditables = ['name', 'email', 'password'];

    /**
     * Datos editables del modelo Account.
     *
     * @var array
     */
    protected $accountEditables = ['userName', 'suscription_id'];

    /**
     * Reglas de validación.
     *
     * @var array
     */
    protected $rules = [
        'name' => 'nullable|max:255',
        'email' => 'nullable|email|max:255|unique:users',
        'userName' => 'nullable|max:255|unique:accounts|regex:/^[\d\w\_\-\.]+$/',
        'old_password'  => 'nullable|required_with:password',
        'password' => 'nullable|required_with:old_password|confirmed|min:6',
        'suscription_id' => 'nullable|exists:suscriptions,id',
    ];

    /**
     * Devuelve los datos editables del modelo User.
     *
     * @param array $data
     * @return array
     */
    protected function userData($data) {
        return array_only($data, $this->userEditables);
    }

    /**
     * Devuelve los datos editables del modelo Account asociado a User.
     *
     * @param array $data
     * @return array
     */
    protected function accountData($data) {
        return array_only($data, $this->accountEditables);
    }

    /**
     * Actualiza el perfil del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user) {
        $data = $request->all();

        $validator = Validator::make($data, $this->rules);
        
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            
            return response()->json([
                'success'=> false,
                'error'=> $error
            ]);
        }

        if(array_has($data, 'password')) {
            if( !Hash::check($data['old_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'old_password' => 'La contraseña es incorrecta'
                    ],
                ]);
            }
            
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($this->userData($data));
        $user->account->update($this->accountData($data));

        if(array_has($data, 'suscription_id')) {
            $suscription = Suscription::find($data['suscription_id']);

            $user->account->suscription()->associate($suscription);
            $user->account->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'user' => $user,
        ]);
    }
}
