<?php

namespace App\Http\Controllers;

use App\Models\ContributionType;
use App\Models\ContributionPeriod;
use App\Models\ContributionPayment;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContributionController extends Controller
{
    // -------------------------------------------------------------
    // ContributionType (Jenis Iuran) CRUD
    // -------------------------------------------------------------
    public function getTypes()
    {
        $types = ContributionType::orderBy('name')->get();
        return $this->successResponse($types, "Berhasil mendapatkan jenis iuran");
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $type = ContributionType::create($validated);
        return $this->successResponse($type, "Berhasil membuat jenis iuran baru");
    }

    public function updateType(Request $request, ContributionType $type)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $type->update($validated);
        return $this->successResponse($type, "Berhasil memperbarui jenis iuran");
    }

    public function destroyType(ContributionType $type)
    {
        $type->delete();
        return $this->successResponse(null, "Berhasil menghapus jenis iuran");
    }

    // -------------------------------------------------------------
    // ContributionPeriod CRUD
    // -------------------------------------------------------------
    public function getPeriods(ContributionType $type)
    {
        $periods = ContributionPeriod::where('contribution_type_id', $type->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        return $this->successResponse($periods, "Berhasil mendapatkan periode iuran");
    }

    public function getAllPeriods()
    {
        $periods = ContributionPeriod::with('contributionType')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        $data = $periods->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->contributionType->name . ' - ' . $this->getMonthName($p->month) . ' ' . $p->year,
                'amount_per_resident' => $p->amount_per_resident,
                'month' => $p->month,
                'year' => $p->year
            ];
        });

        return $this->successResponse($data, "Berhasil mendapatkan seluruh periode iuran");
    }

    public function storePeriod(Request $request, ContributionType $type)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount_per_resident' => 'required|numeric|min:0',
        ]);

        $exists = ContributionPeriod::where('contribution_type_id', $type->id)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return $this->errorResponse("Periode tersebut sudah dibuat untuk jenis iuran ini.", 422);
        }

        return DB::transaction(function () use ($type, $validated) {
            $validated['contribution_type_id'] = $type->id;
            $period = ContributionPeriod::create($validated);

            // Generate payments template for all active residents
            $residents = Resident::all();
            foreach ($residents as $resident) {
                ContributionPayment::create([
                    'contribution_period_id' => $period->id,
                    'resident_id' => $resident->id,
                    'amount_paid' => 0,
                    'payment_status' => 'UNPAID',
                ]);
            }

            return $this->successResponse($period, "Berhasil membuat periode iuran dan memicu catatan pembayaran warga");
        });
    }

    public function destroyPeriod(ContributionPeriod $period)
    {
        $period->delete();
        return $this->successResponse(null, "Berhasil menghapus periode iuran");
    }

    // -------------------------------------------------------------
    // ContributionPayment management
    // -------------------------------------------------------------
    public function getPayments(ContributionPeriod $period)
    {
        // Self-healing: Ensure every resident has a payment record for this period
        $residents = Resident::all();
        $existingResidentIds = ContributionPayment::where('contribution_period_id', $period->id)
            ->pluck('resident_id')
            ->toArray();

        $missingResidentIds = $residents->pluck('id')->diff($existingResidentIds);

        if ($missingResidentIds->isNotEmpty()) {
            foreach ($missingResidentIds as $residentId) {
                ContributionPayment::create([
                    'contribution_period_id' => $period->id,
                    'resident_id' => $residentId,
                    'amount_paid' => 0,
                    'payment_status' => 'UNPAID',
                ]);
            }
        }

        $payments = ContributionPayment::where('contribution_period_id', $period->id)
            ->with(['resident.coordinator'])
            ->get();
        
        $data = $payments->map(function ($pay) {
            return [
                'id' => $pay->id,
                'resident_id' => $pay->resident_id,
                'name' => $pay->resident->name,
                'block' => $pay->resident->block,
                'house_number' => $pay->resident->house_number,
                'coordinator_name' => $pay->resident->coordinator ? $pay->resident->coordinator->name : 'Tanpa Koordinator',
                'amount_paid' => $pay->amount_paid,
                'payment_status' => $pay->payment_status,
                'paid_at' => $pay->paid_at ? $pay->paid_at->toIso8601String() : null
            ];
        });

        // Add aggregate summaries
        $summary = [
            'total_paid_amount' => $payments->where('payment_status', 'PAID')->sum('amount_paid'),
            'total_paid_residents' => $payments->where('payment_status', 'PAID')->count(),
            'total_unpaid_residents' => $payments->where('payment_status', 'UNPAID')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => "Berhasil mendapatkan catatan pembayaran warga",
            'data' => $data,
            'summary' => $summary
        ]);
    }

    public function updatePayment(Request $request, ContributionPayment $payment)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:UNPAID,PAID',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        $period = $payment->contributionPeriod;

        if ($validated['payment_status'] === 'PAID') {
            $payment->payment_status = 'PAID';
            $payment->amount_paid = $validated['amount_paid'] ?? $period->amount_per_resident;
            $payment->paid_at = now();
        } else {
            $payment->payment_status = 'UNPAID';
            $payment->amount_paid = 0;
            $payment->paid_at = null;
        }

        $payment->save();

        // Automatically sync with linked transactions if any exist!
        $this->syncLinkedTransactions($period);

        return $this->successResponse($payment, "Berhasil merubah status pembayaran warga");
    }

    private function syncLinkedTransactions(ContributionPeriod $period)
    {
        // Find transactions linked to this contribution period
        $transactions = \App\Models\Transaction::where('contribution_period_id', $period->id)->get();
        if ($transactions->isEmpty()) return;

        // Sum the total payments for this period
        $totalPaid = ContributionPayment::where('contribution_period_id', $period->id)
            ->where('payment_status', 'PAID')
            ->sum('amount_paid');

        foreach ($transactions as $t) {
            $t->update([
                'amount' => $totalPaid
            ]);
        }
    }

    private function getMonthName($monthNum)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$monthNum] ?? '';
    }
}
