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
}
