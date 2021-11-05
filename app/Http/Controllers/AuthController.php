<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Spatie\Activitylog\Models\Activity;
use Validator;

use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /**
    * Create user
    *
    * @param  [string] username
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] message
    */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email'=>'required|string|unique:users',
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);

        $user = new User([
            'username'  => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->status = 1; // set status to active

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            // activity log
            activity()
            ->causedBy($user)
            ->performedOn($user)
            ->tap(function(Activity $activity) {
                $activity->type = 'auth';
            })
            ->log('User sign up');

            return response()->json([
            'message' => 'Successfully created user!',
            'accessToken'=> $token,
            ],201);
        }
        else {
            return response()->json(['error'=>'Provide proper details']);
        }
    }

    /**
     * Login user and create token
    *
    * @param  [string] email
    * @param  [string] password
    * @param  [boolean] remember_me
    */
    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
        'remember_me' => 'boolean'
        ]);

        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials))
        {
          return response()->json([
              'message' => 'Invalid email or password'
          ],401);
        }

        $user = $request->user();
        $user->status = 1; // set status to active

        if($user->save()){
          $tokenResult = $user->createToken('Personal Access Token');
          $token = $tokenResult->plainTextToken;

          // activity log
          activity()
            ->causedBy($user)
            ->performedOn($user)
            ->tap(function(Activity $activity) {
                $activity->type = 'auth';
            })
            ->log('User login');

          return response()->json([
            'accessToken' =>$token,
            'token_type' => 'Bearer',
          ]);
        }
        else{
            return response()->json(['error'=>'User save failed!']);
        }
    }

    /**
     * Get the authenticated User
    *
    * @return [json] user object
    */
    public function user(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        return response()->json([
          'userData' => User::with('role','subscriptions.plan')->find($user_id)
        ]);
    }

    /**
     * Logout user (Revoke the token)
    *
    * @return [string] message
    */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $user = $request->user();
        $user->status = 2; // set status to inactive

        if($user->save()){

            // activity log
            activity()
            ->causedBy($user)
            ->performedOn($user)
            ->tap(function(Activity $activity) {
                $activity->type = 'auth';
            })
            ->log('User logout');

            return response()->json([
            'message' => 'Successfully logged out'
          ]);
        }
        else{
            return response()->json(['error'=>'User save failed!']);
        }
    }

    /**
     * Send password reset request
    *
    * @return [string] message
    */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $message = "Mail send successfully";
        } else {
            $message = "Email could not be sent to this email address";
        }

        $response = ['data'=>'','message' => $message];
        return response($response, 200);
    }

    /**
     * Reset password
    *
    * @return [string] message
    */
    public function sendResetResponse(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if($status == Password::PASSWORD_RESET){
          $message = "Password reset successfully";
        }else{
          $message = "Email could not be sent to this email address";
        }

        $response = ['data'=>'','message' => $message];
        return response()->json($response);
    }

}
