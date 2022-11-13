<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $satker = Satker::find($request->satker_id);
        $params = $request->all();

        $validator = Validator::make(
            $params,
            [
                'name' => ['required', 'string', 'max:100'],
                'password' => ['required', 'confirmed', 'min:6', 'unique:users'],
                'nik' => ['required', 'unique:users'],
                'satker_id' => ['required'],
            ],
            [
                'password.unique' => 'User already exist'
            ]
        );

        if (!$satker) {
            return ResponseFormatter::error(message: 'Satker not found', code: 404);
        }

        if ($validator->fails()) {
            return ResponseFormatter::error(message: $validator->errors()->first());
        }

        $user = User::create($params);

        event(new Registered($user));

        Auth::login($user);

        $token = $user->createToken("auth_token")->plainTextToken;

        return ResponseFormatter::success(data: $user, token: $token);
    }
}
