<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatorRequest;
use App\Http\Resources\CoordinatorResource;
use App\Models\Coordinator;
use Illuminate\Http\Request;

class CoordinatorController extends Controller
{
    public function getAllCoordinators()
    {
        $data = Coordinator::orderBy('name')->get();
        return $this->successResponse(CoordinatorResource::collection($data), "Berhasil mendapatkan semua data koordinator");
    }

    public function store(CoordinatorRequest $request)
    {
        $validated = $request->validated();
        $data = Coordinator::create($validated);
        return $this->successResponse(CoordinatorResource::make($data), "Berhasil membuat koordinator");
    }

    public function update(CoordinatorRequest $request, Coordinator $coordinator)
    {
        $validated = $request->validated();
        $coordinator->update($validated);
        return $this->successResponse(CoordinatorResource::make($coordinator), "Berhasil memperbarui koordinator");
    }

    public function destroy(Coordinator $coordinator)
    {
        $coordinator->delete();
        return $this->successResponse(null, "Berhasil menghapus koordinator");
    }
}
