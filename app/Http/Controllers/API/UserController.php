<?php

namespace App\Http\Controllers\API;

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
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->first());
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return ResponseFormatter::error("Old password doesn't match");
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return ResponseFormatter::success(message: "Password successfully updated");
    }
}
