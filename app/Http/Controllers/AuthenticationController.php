<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\user\UserLoginRequest;
use App\Http\Requests\user\UserRegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function register(UserRegisterRequest $request): Response
    {
        $validated = $request->validated();
        if (!$validated) return response()->json(['message' => $validated->errors()]);
        $formData = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $request->password,
        ];
        $formData['password'] = bcrypt($request->password);
        $user = User::create($formData);
        return response()->json([
            'user' => $user,
            'message' => __('auth.register.success')
        ], 201);
    }

    public function login(UserLoginRequest $request): Response
    {
        $validated = $request->validated();
        if (!$validated) return response()->json(['message' => $validated->errors()]);
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            dd($token);

            return response()->json([
                'user' => Auth::user(),
                'token' => $token,
                'message' => __('auth.login.success')
            ], 200);
        }

        return response()->json([
            'error' => __('auth.login.failed')
        ], 401);
    }
}
