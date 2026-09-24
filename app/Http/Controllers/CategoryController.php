<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $request->validate([
            'status' => ['sometimes', 'string', 'in:active,archived,all']
        ]);
        $status = $request->query('status', 'active');
        $categories = Category::orderBy('name')
            ->when($status === 'all', function ($query) {
                $query->withTrashed();
            })
            ->when($status === 'archived', function ($query) {
                $query->onlyTrashed();
            })
            ->get();

        return $this->successResponse(CategoryResource::collection($categories), "Berhasil mendapatkan data kategori");
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        Category::create($validated);
        return $this->successResponse(null, "Berhasil membuat kategori");
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return $this->successResponse(null, "Berhasil menghapus kategori");
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $validated = $request->validated();
        $category->update($validated);
        return $this->successResponse(CategoryResource::make($category), "Berhasil memperbarui kategori");
    }
}
