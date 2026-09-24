<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function getPeriodsByAccount(Request $request, Account $account)
    {
        $query = Period::where('account_id', $account->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($request->user()->role !== 'ADMIN') {
            $query->where('is_published', true);
        }

        $data = $query->get();
        return $this->successResponse($data, "Berhasil mendapatkan semua periode");
    }

    public function store(Request $request, Account $account)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'is_published' => 'sometimes|boolean',
            'start_balance' => 'sometimes|numeric|min:0',
        ]);

        $exists = Period::where('account_id', $account->id)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return $this->errorResponse("Periode tersebut sudah terdaftar di buku kerja ini.", 422);
        }

        $validated['account_id'] = $account->id;
        $period = Period::create($validated);

        return $this->successResponse($period, "Berhasil membuat periode baru", 201);
    }

    public function update(Request $request, Period $period)
    {
        $validated = $request->validate([
            'is_published' => 'sometimes|boolean',
            'start_balance' => 'sometimes|numeric|min:0',
        ]);

        $period->update($validated);
        return $this->successResponse($period, "Berhasil memperbarui status periode");
    }

    public function destroy(Period $period)
    {
        $period->delete();
        return $this->successResponse(null, "Berhasil menghapus periode");
    }
}
