<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Account;

use JWTAuth;
use Validator;
use Illuminate\Mail\Message;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    /**
     * Reglas de validación para el login.
     *
     * @var array
     */
    protected $loginRules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    /**
     * Indica si requiere que el usuario haya verificado 
     * su cuenta para iniciar sesión.
     *
     * @var boolean
     */
    protected $requiresVerification;

    public function __construct() {
        $this->requiresVerification = config('auth.verification');
    }

    /**
     * API Login, on success return JWT Auth token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $input = $request->only('email', 'password');

        $validator = Validator::make($input, $this->loginRules);

        if ($validator->fails()) {
            $error = $validator->messages()->toJson();

            return response()->json([
                'success'=> false,
                'error'=> $error
            ]);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if($this->requiresVerification) {
            $credentials['is_verified'] = 1;
        }

        try {
            if ( !$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Credenciales inválidos. Asegúrate de haber introducido la información correcta y de haber verificado la dirección de correo.'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'error' => 'could_not_create_token'
            ], 500);
        }

        $user = Auth::user();
        $accountFolder = $user->account->folder();

        return response()->json([
            'success' => true,
            'user' => $user,
            'accountFolder' => $accountFolder,
            'data'=> [ 
                'token' => $token 
            ],
        ]);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param \Illuminate\Http\Request $request
     */
    public function logout(Request $request) {
        $this->validate($request, ['token' => 'required']);
        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => false,
                'error' => 'Failed to logout, please try again.'
            ], 500);
        }
    }
}
