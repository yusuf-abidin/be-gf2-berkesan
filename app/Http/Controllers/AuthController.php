<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\RegistrationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $code = RegistrationCode::where('code', $validated['registration_code'])->first();
        if ($code->expires_at < now()) {
            return $this->errorResponse("Kode registrasi sudah tidak aktif", 400);
        }
        if ($code->usage_limit && $code->current_usage >= $code->usage_limit) {
            return $this->errorResponse("Kode registrasi sudah tidak bisa digunakan lagi", 400);
        }
        $validated['password'] = \Hash::make($validated['password']);

        $validated['role'] = 'USER';

        $user = DB::transaction(function () use ($validated, $code) {
            $user = User::create($validated);
            $code->increment('current_usage');
            return $user;
        });

        return $this->successResponse(null, "Registrasi berhasil");
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse("Email atau password salah", 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], "Login berhasil");
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, "Logout berhasil");
    }
}
