<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function addUser(Request $request)
    {

        // if get method then return login view
        if ($request->isMethod('get')) {
            return view('add_user');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        if ($user->isAdmin()) {
            $token = $user->createToken('api-token', ['admin'])->plainTextToken;
        } else {
            $token = $user->createToken('api-token', ['customer'])->plainTextToken;
        }

        if (!$request->expectsJson()) {
            //redirect to transaction page
            return redirect()->route('transaction.index');
        }

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function login(Request $request)
    {
        // if get method then return login view
        if ($request->isMethod('get')) {
            return view('login');
        }

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $request->user();

        if ($user->isAdmin()) {
            $token = $user->createToken('api-token', ['admin'])->plainTextToken;
        } else {
            $token = $user->createToken('api-token', ['customer'])->plainTextToken;
        }

        if (!$request->expectsJson()) {
            //redirect to transaction page
            return redirect()->route('transaction.index');
        }

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        if (!$request->expectsJson()) {
            Auth::logout();
            return redirect()->route('login');
        }

        $currentUserAccessToken = $request->user()->currentAccessToken();
        $currentUserAccessToken->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
