<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

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
            201
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

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response('Credentials are invalid', 401);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logoutuser(Request $request)
    {
        // $token = $request->header('Authorization');
        $token = $request->bearerToken();

        if ($token) {
            $tokenInstance = PersonalAccessToken::findToken($token);
        }

        if ($tokenInstance) {
            // $user = $tokenInstance->tokenable; // to get user
            // dd($user);
            $tokenInstance->delete();
            return response(
                ['message' => 'User Logged out'],
                200
            );
        }

        // DB::table('personal_access_tokens')
        //     ->where('token', $token)
        //     ->delete(); // inefficient way / plus the tokens are hashed in the database

        return response(
            ['message' => 'Something went wrong'],
            422
        );
    }
}
