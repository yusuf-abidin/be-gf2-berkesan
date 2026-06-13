<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationCodeRequest;
use App\Http\Resources\RegistrationCodeResource;
use App\Models\RegistrationCode;
use Illuminate\Http\JsonResponse;

class RegistrationCodeController extends Controller
{
    public function getRegistrationCode(): JsonResponse
    {
        $codes = RegistrationCode::all();
        $data =RegistrationCodeResource::collection($codes);

        return $this->successResponse($data, "Berhasil mendapatkan data kode registrasi");
    }
    public function store(RegistrationCodeRequest $request)
    {
        $validated = $request->validated();
        $validated['expires_at'] = now()->addMinutes($validated['expires_at']);
        RegistrationCode::create($validated);

        return $this->successResponse(null, "Berhasil membuat kode registrasi");
    }

    public function destroy(RegistrationCode $code)
    {
        $code->delete();
        return $this->successResponse(null, "Berhasil menghapus kode registrasi");
    }

}
