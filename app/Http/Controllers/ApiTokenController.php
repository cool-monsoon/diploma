<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ApiTokenController extends Controller
{
    public function createToken(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['required', 'string'],
        ]);


        if ($validation->fails()) {
            return response()->json(['err' => $validation->errors()]);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['err' => 'The provided credentials are incorrect'], 401);
        }
        $token = $user->createToken($request->device_name);
        return ['token' => $token->plainTextToken];
    }
}
