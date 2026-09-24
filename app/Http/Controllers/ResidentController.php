<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Models\Resident;

class ResidentController extends Controller
{

    public function getAllResidents()
    {
        $data = Resident::orderBy('name')->get();
        return $this->successResponse(ResidentResource::collection($data), "Berhasil mendapatkan semua data warga");
    }

    public function store(ResidentRequest $request)
    {
        $validated = $request->validated();
         $data = Resident::create($validated);
        return $this->successResponse(new ResidentResource($data), "Berhasil membuat data warga");
    }

    public function update(UpdateResidentRequest $request, Resident $resident)
    {
        $validated = $request->validated();
        $resident->update($validated);
        return $this->successResponse(new ResidentResource($resident), "Berhasil memperbarui data warga");
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();
        return $this->successResponse(null, "Berhasil menghapus data warga");

    }
}
