<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        $data = UserResource::make($user);
        return $this->successResponse($data, "Berhasil mendapatkan data user");
    }

    public function getAllUsers()
    {
        $users = User::orderBy('name')->get();
        return $this->successResponse(UserResource::collection($users), "Berhasil mendapatkan semua data user");
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:ADMIN,USER',
        ]);

        // Prevent demoting self to avoid accidental lockout
        if ($user->id === $request->user()->id && $validated['role'] !== 'ADMIN') {
            return $this->errorResponse("Anda tidak dapat mencabut akses Admin Anda sendiri demi menghindari terkunci dari sistem.", 400);
        }

        $user->update([
            'role' => $validated['role']
        ]);

        return $this->successResponse(UserResource::make($user), "Berhasil mengubah peran pengguna");
    }
}
