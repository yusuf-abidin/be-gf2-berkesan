<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Generate a one-time password reset link for a user (Admin only).
     */
    public function generate(Request $request, User $user)
    {
        // Invalidate previous requests for this user
        PasswordResetRequest::where('user_id', $user->id)->delete();

        // Generate 64 characters secure random token
        $token = Str::random(64);

        // Expiry set to 20 minutes from now
        $expiresAt = now()->addMinutes(20);

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $resetUrl = "{$frontendUrl}/reset-password?token={$token}";

        return $this->successResponse([
            'reset_url' => $resetUrl,
            'expires_at' => $expiresAt->toIso8601String(),
            'expires_in_minutes' => 20,
        ], "Berhasil membuat tautan reset password");
    }

    /**
     * Verify token validity (Public).
     */
    public function verify(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return $this->errorResponse("Token tidak disediakan.", 400);
        }

        $resetRequest = PasswordResetRequest::where('token', $token)->first();

        if (!$resetRequest) {
            return $this->errorResponse("Tautan reset password tidak valid.", 400);
        }

        if ($resetRequest->is_used) {
            return $this->errorResponse("Tautan reset password ini sudah pernah digunakan.", 400);
        }

        if ($resetRequest->expires_at->isPast()) {
            return $this->errorResponse("Tautan reset password ini sudah kedaluwarsa.", 400);
        }

        return $this->successResponse([
            'email' => $resetRequest->user->email,
        ], "Token reset password valid.");
    }

    /**
     * Reset password using token (Public).
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $request->token)->first();

        if (!$resetRequest) {
            return $this->errorResponse("Tautan reset password tidak valid.", 400);
        }

        if ($resetRequest->is_used) {
            return $this->errorResponse("Tautan reset password ini sudah pernah digunakan.", 400);
        }

        if ($resetRequest->expires_at->isPast()) {
            return $this->errorResponse("Tautan reset password ini sudah kedaluwarsa.", 400);
        }

        $user = $resetRequest->user;
        $user->password = Hash::make($request->password);
        $user->save();

        // Mark the request as used
        $resetRequest->is_used = true;
        $resetRequest->save();

        return $this->successResponse(null, "Password berhasil diperbarui.");
    }
}
