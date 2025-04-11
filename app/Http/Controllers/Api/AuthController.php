<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! auth()->attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user  = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            'acces_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }
    public function register(Request $request)
    {
        //validator
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            'message' => 'Register Success',
            'user'    => $user,
        ], 201);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout Success',
        ]);
    }
}
