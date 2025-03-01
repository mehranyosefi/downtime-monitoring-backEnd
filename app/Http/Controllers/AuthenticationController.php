<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\AuthLoginRequest;
use App\Http\Requests\auth\AuthRegisterRequest;
use App\Http\Requests\auth\AuthResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthenticationController extends Controller
{
    public function register(AuthRegisterRequest $request): Response
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
            'user' => new UserResource($user),
            'message' => __('auth.register.success')
        ], 201);
    }

    public function login(AuthLoginRequest $request): Response
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
            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
                'message' => __('auth.login.success')
            ], 200);
        }

        return response()->json([
            'error' => __('auth.login.failed')
        ], 401);
    }

    public function forget_password(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);
        $user = User::where('email', $request->email)->first();
        $status = null;
        if ($user) {
            $status = Password::broker('users')->sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => __($status)])
                : response()->json(['message' => __($status)], 400);
        }
        $status = PasswordBroker::INVALID_USER;
        return response()->json(['message' => __($status)], 404);
    }

    public function reset_password(AuthResetPasswordRequest $request)
    {
        $validated = $request->validated();
        if (!$validated) return response()->json(['message' => $validated->errors()]);
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }
    public function log_out()
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => __("auth.logout.success")
        ];
    }
}
