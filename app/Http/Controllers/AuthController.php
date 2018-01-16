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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Undocumented variable
     *
     * @var \App\Services\UserService
     */
    protected $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'userName' => 'required|max:255|regex:/^[\d\w\_\-\.]+$/',
            'password' => 'required|confirmed|min:6',
            'suscription' => 'required|exists:suscriptions,id',
        ];

        $input = $request->only(
            'name',
            'email',
            'userName',
            'password',
            'password_confirmation',
            'suscription'
        );

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            
            return response()->json([
                'success'=> false,
                'error'=> $error
            ]);
        }

        $user = $this->userService->createUser($input);

        $verificationCode = str_random(30); //Generate verification code
        DB::table('user_verifications')->insert([
            'user_id'=>$user->id,
            'token'=>$verificationCode
        ]);
        
        $appName = env('APP_NAME', 'HyperCloud');
        $subject = "Por favor, verifique su cuenta de $appName.";

        Mail::send('email.verification', ['name' => $user->name, 'verificationCode' => $verificationCode],
            function($mail) use ($user, $subject, $appName) {
                $mail->from(env('MAIL_FROM_ADDRESS'), $appName);
                $mail->to($user->email, $user->name);
                $mail->subject($subject);
            });

        return response()->json([
            'success'=> true,
            'message'=> '¡Gracias por registrarte! Por favor, comprueba tu email para completar el registro.'
        ]);
    }

    /**
     * API Verify User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser($verificationCode) {

        $check = DB::table('user_verifications')->where('token', $verificationCode)->first();
        if(!is_null($check)) {
            $user = User::find($check->user_id);
            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'La cuenta ya ha sido verificada...'
                ]);
            }

            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verificationCode)->delete();

            return response()->json([
                'success'=> true,
                'message'=> 'La cuenta ha sido verificada con éxito.'
            ]);
        }

        return response()->json([
            'success'=> false,
            'error'=> 'El código de verificación no es válido.'
        ]);
    }

    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $input = $request->only('email', 'password');

        $validator = Validator::make($input, $rules);
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
            'is_verified' => 1
        ];

        try {
            // attempt to verify the credentials and create a token for the user
            if ( !$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Credenciales inválidos. Asegúrate de haber introducido la información correcta y de haber verificado la dirección de correo.'
                ], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => false,
                'error' => 'could_not_create_token'
            ], 500);
        }

        $user = Auth::user();
        $accountFolder = $user->account->folder();

        // all good so return the token
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
     * @param Request $request
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
