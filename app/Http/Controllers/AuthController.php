<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->all();

        $errors = Validator::make($fields, [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        return response(
            [
                'user' => $user,
                'message' => 'Your account was created!'
            ],
            200
        );
    }

    public function login(Request $request)
    {
        $fields = $request->all();
        $errors = Validator::make($fields, [
            'email' => 'email|required',
            'password' => 'required|min:6'
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::where('email', $fields['email']);

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response('Credentials are invalid', 401);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }
}
