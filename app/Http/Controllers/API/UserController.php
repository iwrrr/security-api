<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        if (!$request->user()) {
            return ResponseFormatter::error(message: "Unauthenticated", code: 401);
        }

        return ResponseFormatter::success($request->user());
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6', 'unique:users'],
        ]);

        if (!$user) {
            return ResponseFormatter::error(message: 'User not found', code: 404);
        }

        if ($validator->fails()) {
            return ResponseFormatter::error(message: $validator->errors()->first());
        }

        if ($request->old_password != $user->password) {
            return ResponseFormatter::error(message: "Old password doesn't match");
        }

        $user->update([
            'password' => $request->password
        ]);

        return ResponseFormatter::success(message: "Password successfully updated");
    }

    public function forgotPassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', 'min:6', 'unique:users'],
        ]);

        if (!$user) {
            return ResponseFormatter::error(message: 'User not found', code: 404);
        }

        if ($validator->fails()) {
            return ResponseFormatter::error(message: $validator->errors()->first());
        }

        $user->update([
            'password' => $request->password
        ]);

        return ResponseFormatter::success(message: "Password successfully updated");
    }
}
