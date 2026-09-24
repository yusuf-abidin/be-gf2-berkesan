<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function getCategoriesByAccount(Request $request, Account $account)
    {
        $categories = $account->categories()
            ->orderBy('name')
            ->get();

        $data = CategoryResource::collection($categories);

        return $this->successResponse($data, "Berhasil mendapatkan data kategori berdasarkan akun");
    }
}
