<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $credentials = request(['email', 'password']);
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
            'client' => 'required_without:provider',
            'provider' => 'required_without:client',
        ]);

        $user = new User([
            'uuid' => Str::uuid()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        DB::transaction(function () use ($user, $request) {
            if ($user->save()) {

                if ($request->exists('client')) {
                    $client = new Client([
                        'uuid' => Str::uuid()->toString(),
                        'user_id' => $user->id,
                    ]);
                    $client->save();
                }

                if ($request->exists('provider')) {
                    $provider = new Provider([
                        'uuid' => Str::uuid()->toString(),
                        'user_id' => $user->id,
                    ]);
                    $provider->save();
                }
            } else {
                return response()->json(['error' => 'Provide proper details']);
            }
        });

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'message' => 'Successfully created user!',
            'accessToken' => $token,
        ], 201);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);

    }
}
