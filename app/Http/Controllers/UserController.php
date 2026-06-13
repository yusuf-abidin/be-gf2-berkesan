<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        $data = UserResource::make($user);
        return $this->successResponse($data, "Berhasil mendapatkan data user");
    }
}
