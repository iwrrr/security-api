<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(message: $validator->errors()->first());
        }

        $user = User::where('password', $request->password)->first();

        if (!$user) {
            return ResponseFormatter::error(message: "User not found.");
        }

        if ($request->password != $user->password) {
            return ResponseFormatter::error(message: "Incorrect password.");
        }

        Auth::login($user);

        $token = $user->createToken("auth_token")->plainTextToken;

        return ResponseFormatter::success(data: $user, token: $token);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
