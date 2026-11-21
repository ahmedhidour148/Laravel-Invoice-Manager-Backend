<?php

namespace App\Http\Controllers\api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerifyEmailCode;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);
        $code = random_int(100000, 999999);
        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => Carbon::now()->addMinutes(10)
        ]);
        Mail::to($user->email)->send(new VerifyEmailCode($code));
        UserRegistered::dispatch($user);
        return response()->json([
            'msg' => 'register successfully and code verfication sent \n check your email for code OTP',
            'data' => $user
        ]);
    }
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        if (!Auth::attempt($request->only("email", "password"))) {
            return response()->json(["msg" => "invalid information"], 401);
        }
        $user = User::where("email", $request->email)->firstOrFail();
        if (!$user->hasVerifiedEmail()) {
            return response()->json(["msg" => "please verfiy email first"], 403);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            "msg" => "login successfully ",
            "token" => $token
        ], 200);

    }
    public function logout(request $request)
    {
        $request->user()->CurrentAccessToken()->delete();
        return response()->json(['msg' => 'logout successfully']);
    }
    public function verfiyemail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string'
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'msg' => 'email is alredy verified.'
            ], 200);
        }
        if ($user->verification_code !== $request->code) {
            return response()->json([
                'msg' => 'invalid code.'
            ], 422);
        }
        if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
            return response()->json([
                'msg' => 'verfication code has expired.'
            ], 422);
        }
        $user->markEmailAsVerified();
        $user->update([
            'verification_code' => null,
            'verification_code_expires_at' => null
        ]);
        return response()->json([
            'msg' => 'email verified successfully'
        ], 200);
    }
    public function resendotpcode(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email|exists:users,email"
        ]);
        $user = User::where('email', $validated['email'])->firstOrFail();
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'msg' => 'email is alredy verified.'
            ], 200);
        }
        $code = random_int(10000, 999999);
        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => Carbon::now()->addMinutes(10)
        ]);
        Mail::to($user->email)->send(new VerifyEmailCode($code));
        return response()->json([
            "msg" => "resend code successfully,check your email"
        ], 200);
    }
}
