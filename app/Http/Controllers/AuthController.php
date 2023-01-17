<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        if (empty($username)) {
            return response()->json(['status' => 'error', 'message' => 'Need username']);
        }

        if (empty($password)) {
            return response()->json(['status' => 'error', 'message' => 'Need password']);
        }

        // Check if password is greater than 5 character
        if (strlen($password) < 6) {
            return response()->json(['status' => 'error', 'message' => 'Password should be min 6 character']);
        }

        // Check if user already exist
        if (User::where('username', '=', $username)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User already exists with this username']);
        }

        // Create new user
        try {
            $user = new User();
            $user->username = $request->username;
            $user->password = app('hash')->make($request->password);

            if ($user->save()) {
                return $this->login($request);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        // TODO duplicate codes
        if (empty($username)) {
            return response()->json(['status' => 'error', 'message' => 'Need username']);
        }

        if (empty($password)) {
            return response()->json(['status' => 'error', 'message' => 'Need password']);
        }

        $credentials = request(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
