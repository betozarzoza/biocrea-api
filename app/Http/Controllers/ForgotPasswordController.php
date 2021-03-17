<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;

class ForgotPasswordController extends Controller
{
    protected const EXPIRE_IN_HOURS = 2; // rest link expiration in hours

    /**
     * send reset link 
     *
     * @param Request $request
     * @return void
     */
    public function sendResetLinkEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
        ]);
        if ($validator->fails())
            return response($validator->errors()->messages(), 400);
        
        $token = $this->createToken($request->email); 
        $reset_url = env('WEBAPP_URL', 'http://localhost:8080/') .
            '#/recover-password/' . $token . '/' . $request->email;
        \Mail::to($request->email)->send(new ForgotPassword($reset_url));
        /*
        SendEmail::dispatch(
            'emails.reset-password',
            [
                'email' => $request->email,
                'action_url' => $reset_url, 
                'expireInHours' => self::EXPIRE_IN_HOURS, // link expire
            ],
            'hola@biocrea.mx', // sender
            'Biocrea',
            'Restablecimiento de Contraseña',
            $request->email // recipient
        );
        */
        return response(['message' => 'sending_reset_link'], 200);
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return void
     */
    public function passwordReset(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:password_resets',
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails())
            return response($validator->errors()->messages(), 400);
        $isTokenValid = $this->tokenValid($request->email, $request->token);
        if ($isTokenValid == 1 ) { // token valid 
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $this->deleteToken($request->email, $request->token);
            return response(['message' => 'password_updated_succesfully'], 200);
        }
        if ( $isTokenValid == 0 ) { // token expired
            $this->deleteToken($request->email, $request->token);
            return response(['message' => 'rest_password_token_expired'], 406); 
        }
        return response(['message' => 'operation_not_acceptable'], 406);
    }

    /**
     * Generate token if not exists
     *
     * @param string $email
     * @return void
     */
    public function createToken($email) {
        $tokenExists = DB::table('password_resets')->where('email', $email)->first();
        if ( $tokenExists ) {
            return $tokenExists->token;
        }
        $token = Str::random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    /**
     * Save token generated
     *
     * @param [type] $token
     * @return void
     */
    public function saveToken($token, $email) {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    /**
     * Token validation; if exists, if has not expired and if is valid
     *
     * @param [type] $email
     * @param [type] $token
     * @return void
     */
    public function tokenValid($email, $token) {
        $token = DB::table('password_resets')->where([
            'email' => $email,
            'token' => $token
        ])->first();
        if ( $token ) 
            return Carbon::create($token->created_at)->addHour(self::EXPIRE_IN_HOURS) > Carbon::now() ? 1 : 0; 
        return -1; // token doesnt exists
    }

    /**
     * Delete token
     *
     * @param [type] $email
     * @param [type] $token
     * @return void
     */
    public function deleteToken($email, $token) {
        $token = DB::table('password_resets')->where([
            'email' => $email,
            'token' => $token
        ])->delete();
    }
}
