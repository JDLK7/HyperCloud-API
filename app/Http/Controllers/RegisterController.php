<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Servicio Usuario.
     *
     * @var \App\Services\UserService
     */
    protected $userService;

    /**
     * Reglas de validación para el registro.
     *
     * @var array
     */
    protected $registrationRules = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:users',
        'userName' => 'required|max:255|regex:/^[\d\w\_\-\.]+$/',
        'password' => 'required|confirmed|min:6',
        'suscription' => 'required|exists:suscriptions,id',
    ];

    /**
     * Crea y envía el código de verificación a 
     * un usuario que se acaba de registrar.
     *
     * @param \App\User $user
     * @return void
     */
    protected function sendVerification(User $user) {
        $verificationCode = str_random(30);

        DB::table('user_verifications')->insert([
            'user_id'   => $user->id,
            'token'     => $verificationCode,
        ]);
        
        $appName = config('app.name');
        $subject = "Por favor, verifique su cuenta de $appName.";

        Mail::send('email.verification', ['name' => $user->name, 'verificationCode' => $verificationCode],
            function ($mail) use ($user, $subject, $appName) {
                $mail->from(env('MAIL_FROM_ADDRESS'), $appName);
                $mail->to($user->email, $user->name);
                $mail->subject($subject);
            });
    }

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * Registro del API.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $input = $request->only(
            'name',
            'email',
            'userName',
            'password',
            'password_confirmation',
            'suscription'
        );

        $validator = Validator::make($input, $this->registrationRules);

        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            
            return response()->json([
                'success'=> false,
                'error'=> $error
            ]);
        }

        $user = $this->userService->createUser($input);

        $this->sendVerification($user);

        return response()->json([
            'success'=> true,
            'message'=> '¡Gracias por registrarte! Por favor, comprueba tu email para completar el registro.'
        ]);
    }

    /**
     * Verificación del usuario.
     *
     * @param string $verificationCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser($verificationCode) {
        
        $check = DB::table('user_verifications')
            ->where('token', $verificationCode)->first();

        if(!is_null($check)) {
            $user = User::find($check->user_id);

            if($user->is_verified == 1) {
                return response()->json([
                    'success'=> true,
                    'message'=> 'La cuenta ya ha sido verificada...'
                ]);
            }

            $user->update(['is_verified' => 1]);

            DB::table('user_verifications')
                ->where('token', $verificationCode)->delete();

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
}
