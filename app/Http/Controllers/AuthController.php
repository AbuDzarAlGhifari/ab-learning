<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function register(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|exists:roles,name'
        ]);
        // Ambil ID role
        $role = Role::where('name', $data['role'])->first();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role->id
        ]);
        $token = $user->createToken('api')->plainTextToken;
        return response(compact('user', 'token'), 201);
    }

    public function login(Request $req)
    {
        $data = $req->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $data['email'])->firstOrFail();
        if (!Hash::check($data['password'], $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('api')->plainTextToken;
        return response(compact('user', 'token'));
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response('', 204);
    }
}
