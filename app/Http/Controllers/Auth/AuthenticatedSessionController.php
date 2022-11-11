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
            'login' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->first());
        }

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $fieldValue = $request->login;
        $request->merge([$fieldType => $fieldValue]);

        if (Auth::attempt($request->only($fieldType, 'password'))) {
            $user = User::where($fieldType, $fieldValue)->first();

            if (!$user) {
                return ResponseFormatter::error("User not found.");
            }

            if (!Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error("Password is wrong.");
            }

            Auth::login($user);

            $token = $user->createToken("auth_token")->plainTextToken;

            return ResponseFormatter::success($user, $token);
        } else {
            return ResponseFormatter::error("Incorrect Username or Password.");
        }
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
