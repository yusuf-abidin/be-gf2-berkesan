<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAllAccounts(Request $request)
    {
        $accounts = Account::all();
        return $this->successResponse(AccountResource::collection($accounts), "Berhasil mendapatkan semua data akun");
    }

    public function getAccountById(Account $account)
    {
        return $this->successResponse(new AccountResource($account), "Berhasil mendapatkan data akun");
    }

    public function store(AccountRequest $request)
    {
        $validated = $request->validated();
        Account::create($validated);
        return $this->successResponse(null, "Berhasil membuat akun");
    }

    public function update(AccountRequest $request, Account $account)
    {
        $validated = $request->validated();
        $account->update($validated);
        return $this->successResponse(new AccountResource($account), "Berhasil memperbarui akun");
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return $this->successResponse(null, "Berhasil menghapus akun");
    }
}
