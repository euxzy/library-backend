<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // validasi request yang diinputkan
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'logo' => 'image|max:2048',
            'description' => 'required'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Add category failed!'
            ]);
        }

        $validated = $validator->validated();

        $validated['logo'] = $request->getSchemeAndHttpHost() . '/storage/' . 'images/logo.png';

        if ($request->hasFile('logo')) {
            $logo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('logo')->store('images', 'public');
            $validated['logo'] = $logo;
        }

        Category::create($validated);
        return response()->json([
            'status' => true,
            'message' => 'Add category success!',
            'data' => $validated
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::query()->find($id);

        // cek ada atau tidak nya category di database
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'max:50',
            'logo' => 'image|max:2048',
            'description' => 'min:5'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Update category failed!'
            ]);
        }

        $validated = $validator->validated();

        if ($request->hasFile('logo')) {
            $oldLogo = Str::of($category->logo)->remove($request->getSchemeAndHttpHost() . '/storage');
            if (Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('logo')->store('images', 'public');
            $validated['logo'] = $logo;
        }

        $category->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'Update Category Success!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::query()->find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        $oldLogo = Str::of($category->logo)->remove($request->getSchemeAndHttpHost() . '/storage');
        // cek apakah gambar ada di storage
        if (Storage::disk('public')->exists($oldLogo)) {
            // jika ada, maka akan menghapus gambar dari storage
            Storage::disk('public')->delete($oldLogo);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delete Data Category Success!'
        ]);
    }

    public function index()
    {
        $books = Book::query()->get();
        $books->makeHidden([
            'id_author',
            'id_category',
            'created_at',
            'updated_at'
        ]);

        $categories = Category::query()->get()
            ->map(function ($category) use ($books) {
                $category['books'] = $books->filter(fn ($book) => $book->id_category == $category['id']);
                return $category;
            });

        return response()->json([
            'status' => true,
            'message' => 'Get Data Success!',
            'data' => $categories->makeHidden([
                'created_at',
                'updated_at'
            ])
        ]);
    }

    public function show($id)
    {
        $books = Book::query()->get();
        $books->makeHidden([
            'id_author',
            'id_category',
            'created_at',
            'updated_at'
        ]);

        $category = Category::query()->where('id', $id)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        $category['books'] = $books->filter(fn ($book) => $book->id_author == $category->id);

        return response()->json([
            'status' => true,
            'message' => 'Get Data Success!',
            'data' => $category->makeHidden([
                'created_at',
                'updated_at'
            ])
        ]);
    }
}
