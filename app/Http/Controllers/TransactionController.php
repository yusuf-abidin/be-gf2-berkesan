<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Period;
use App\Models\ContributionPayment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getTransactionsByPeriod(Request $request, Period $period)
    {
        // Permission check for non-ADMIN role and draft status
        if ($request->user()->role !== 'ADMIN' && !$period->is_published) {
            return $this->errorResponse("Laporan periode ini belum dipublikasikan oleh Admin.", 403);
        }

        $transactions = Transaction::where('period_id', $period->id)
            ->with('category')
            ->orderBy('day', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $summary = [
            'start_balance' => (float) $period->start_balance,
            'total_in' => (float) $transactions->where('type', 'IN')->sum('amount'),
            'total_out' => (float) $transactions->where('type', 'OUT')->sum('amount'),
            'balance' => (float) ($period->start_balance + $transactions->where('type', 'IN')->sum('amount') - $transactions->where('type', 'OUT')->sum('amount')),
        ];

        return response()->json([
            'success' => true,
            'message' => "Berhasil mendapatkan data transaksi",
            'data' => $transactions,
            'summary' => $summary
        ]);
    }

    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();

        // Auto-calculate amount if linked to a contribution period
        if ($validated['contribution_period_id'] ?? null) {
            $totalPaid = ContributionPayment::where('contribution_period_id', $validated['contribution_period_id'])
                ->where('payment_status', 'PAID')
                ->sum('amount_paid');
            $validated['amount'] = $totalPaid;
            $validated['type'] = 'IN';
        }

        $data = Transaction::create($validated);
        return $this->successResponse($data, "Berhasil membuat transaksi");
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();

        if ($validated['contribution_period_id'] ?? null) {
            $totalPaid = ContributionPayment::where('contribution_period_id', $validated['contribution_period_id'])
                ->where('payment_status', 'PAID')
                ->sum('amount_paid');
            $validated['amount'] = $totalPaid;
            $validated['type'] = 'IN';
        }

        $transaction->update($validated);
        return $this->successResponse($transaction, "Berhasil memperbarui transaksi");
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return $this->successResponse(null, "Berhasil menghapus transaksi");
    }
}
