<?php

namespace App\Http\Controllers\Api\Auth;
use App\Models\User;
use http\Env\Response;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRegisterRequest;
use App\Http\Requests\Api\VerificationRequest;
use App\Http\Requests\Api\EmailVerificationRequest;
use App\Http\Requests\Api\PasswordResetRequest;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api',[
            'except'=> [
                'register',
                'login',
                'emailverification',
                'logout',
                'resendVerificationCode',
                'passwordResetVerificationCode',
                'resendPassword'
            ]
        ]);
    }

    /**
     * @param UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request)
    {
        $user = User::create([
           'first_name' => $request->input('first_name'),
           'last_name' => $request->input('last_name'),
           'email' => $request->input('email'),
           'password' => Hash::make($request->input('password'))
        ]);
        return $this->sendEmailVerificationCodeRespond($user);
    }

    public function resendPassword( PasswordResetRequest $request)
    {
        $user = User::where([
            'email' => $request->email,
            'verification_code' => $request->verification_code
        ])->first();

        if ( !$user ) {
            return response()->json([
               'errors' => [
                   'message' => 'Incorrect Verification Code'
               ]
            ], 400);
        }

        $user->changePassword($request->input('password'));

        return response()->json([
            'message' => 'Password reset successfully'
        ]);

    }

    /**
     * @param EmailVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailverification( EmailVerificationRequest $request)
    {
        $user = User::where([
            'email' => $request->email,
            'verification_code' => $request->verification_code,
            'email_verified_at' => null,
        ])->first();

        if ( !$user ) {
            return response()->json([
                'errors' => [
                    'verification_code' => [
                        'Incorrect verification code'
                    ],
                ],
            ], 400);
        }

        $user->markEmailVerified();
        return response()->json($user);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Successfully logout']);
    }

    public function passwordResetVerificationCode(VerificationRequest $request)
    {
        $user = User::where([
            'email' => $request->input('email')
        ])->first();
        if ( $user ) {
            $user->passwordResetVerificationCode();
        }

        return response()->json([
           'message' => 'Password Reset Verification Code Sent'
        ]);
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login( LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = $this->guard()->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Incorrect email or password'], 401);
        }
        if ( empty(auth()->user()->email_verified_at) ) {
            return response()->json(['error' => 'Please verify your email address for login'], 401);
        }

        return $this->respondWithToken($token);

    }

    /**
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh_token()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * @param VerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationCode(VerificationRequest $request)
    {
        $user = User::where([
            'email' => $request->input('email'),
            'email_verified_at' => null
        ])->first();

        return $this->sendEmailVerificationCodeRespond($user);

    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailVerificationCodeRespond($user)
    {
        if ( $user )
        {
            $user->sendEmailVerificationCode();
        }

        return response()->json([
            'message' => 'Email verification code sent'
        ]);
    }

    /**
     * @return Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
