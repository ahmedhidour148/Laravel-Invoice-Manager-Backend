<?php

namespace App\Http\Controllers\api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailCode;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function register(request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:12|confirmed'
        ]);
        $user = User::create($validated);
        $code = rand(100000, 999999);
        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => Carbon::now()->addMinutes(10)
        ]);
        Mail::to($user->email)->send(new VerifyEmailCode($code));
        UserRegistered::dispatch($user);
        return response()->json([
            'msg' => 'register successfully and code verfication sent',
            'data' => $validated
        ]);
    }
    public function login(request $request)
    {
        $data = $request->validate([
            "email" => "required|string|email",
            "password" => "required",
        ]);
        if (!Auth::attempt($request->only("email", "password"))) {
            return response()->json(["msg" => "invalid information"], 401);
        }
        $user = User::where("email", $request->email)->firstOrFail();
        if(!$user->hasVerifiedEmail()){
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
                'msg' => 'verfication code has expired0.'
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
}
