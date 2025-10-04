<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $request->validate([
                'nama_depan'    => 'required|string|max:255',
                'nama_belakang' => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|string|min:8|confirmed',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            ]);

            $user = User::create([
                'nama_depan'    => $request->nama_depan,
                'nama_belakang' => $request->nama_belakang,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'User registered successfully',
                'data'    => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Registration failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'       => true,
                'message'      => 'Login successful',
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Login failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Logout failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function user(Request $request)
    {
        try {
            return response()->json([
                'status'  => true,
                'message' => 'User retrieved successfully',
                'data'    => $request->user(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to retrieve user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUser(Request $request,$id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'nama_depan'    => 'string|max:255',
                'nama_belakang' => 'string|max:255',
                'email'         => 'email|unique:users,email,' . $user->id,
                'password'      => 'nullable|string|min:8|confirmed',
                'tanggal_lahir' => 'date',
                'jenis_kelamin' => 'in:Laki-laki,Perempuan',
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json([
                'status'  => true,
                'message' => 'User updated successfully',
                'data'    => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
