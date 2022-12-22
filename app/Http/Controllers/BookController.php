<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function store(Request $request)
    {
        // validasi request yang diinputkan
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'id_author' => 'required|numeric',
            'id_category' => 'required|numeric',
            'book_language' => 'required|max:20',
            'total_pages' => 'required|numeric',
            'sinopsis' => 'required',
            'type' => 'required',
            'publisher' => 'required|max:100',
            'published_at' => 'required|date',
            'isbn' => 'required|max:13',
            'photo' => 'image|max:2048',
            'description' => 'required'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Add book failed!'
            ]);
        }

        // mengambil hasil validasi request yang diinput
        $validated = $validator->validated();

        // jika tidak ada gambar yang diinputkan, maka default url akan diubah
        $validated['photo'] = $request->getSchemeAndHttpHost() . '/storage/' . 'images/no_image.png';

        /** 
         * jika terdapat input gambar, maka gambar akan disimpan
         * ke storage dan value photo akan diubah menjadi url
         * dari gambar yang diinputkan
         */
        if ($request->hasFile('photo')) {
            $photo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('photo')->store('images', 'public');
            $validated['photo'] = $photo;
        }

        Book::create($validated); // menambahkan data ke database
        return response()->json([
            'status' => true,
            'message' => 'Add Book Success!',
            'data' => $validated
        ]);
    }

    public function update(Request $request, $id)
    {
        // mencari data buku berdasarkan id
        $book = Book::query()->find($id);

        // cek ada atau tidak nya buku di database
        if (!$book) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }


        // validasi request yang diinputkan
        $validator = Validator::make($request->all(), [
            'name' => 'max:100',
            'id_author' => 'numeric',
            'id_category' => 'numeric',
            'book_language' => 'max:20',
            'total_pages' => 'numeric',
            'sinopsis' => 'min:5',
            'type' => '',
            'publisher' => 'max:100',
            'published_at' => 'date',
            'isbn' => 'max:13',
            'photo' => 'image|max:2048',
            'description' => 'min:5'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Update Book Failed!'
            ]);
        }

        // mengambil hasil validasi request yang diinput
        $validated = $validator->validated();

        /** 
         * jika terdapat input gambar, maka gambar akan disimpan
         * ke storage dan value photo akan diubah menjadi url
         * dari gambar yang diinputkan 
         */
        if ($request->hasFile('photo')) {
            // mendapatkan path file dari photo lama yang ada di database
            $oldPhoto = Str::of($book->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
            // cek apakah gambar ada di storage
            if (Storage::disk('public')->exists($oldPhoto)) {
                // jika ada, maka akan menghapus gambar lama
                Storage::disk('public')->delete($oldPhoto);
            }
            $photo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('photo')->store('images', 'public');
            $validated['photo'] = $photo;
        }

        $book->update($validated); // update data ke database
        return response()->json([
            'status' => true,
            'message' => 'Update Author Success!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // mencari data buku berdasarkan id
        $book = Book::query()->find($id);

        // cek ada atau tidak nya buku di database
        if (!$book) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        // mendapatkan path file dari photo lama yang ada di database
        $oldPhoto = Str::of($book->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
        // cek apakah gambar ada di storage
        if (Storage::disk('public')->exists($oldPhoto)) {
            // jika ada, maka akan menghapus gambar dari storage
            Storage::disk('public')->delete($oldPhoto);
        }

        // menghapus data buku dari database
        $book->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delete Data Book Success!'
        ]);
    }

    public function index()
    {
        $authors = Author::query()->get(); // mengambil semua data author
        $categories = Category::query()->get(); // mengambil semua data author
        $books = Book::query()->get() // mengambil semua data buku
            ->map(function ($book) use ($categories, $authors) {
                /**
                 * menambahkan data author dan category agar
                 * bisa tampil di data buku
                 */
                $book['author'] = $authors->filter(fn ($author) => $author->id == $book->id_author);
                $book['category'] = $categories->filter(fn ($category) => $category->id, $book->id_category);

                /**
                 * mengecualikan data yang tidak ingin ditampilkan
                 */
                $book->author->makeHidden([
                    'created_at',
                    'updated_at'
                ]);
                $book->category->makeHidden([
                    'created_at',
                    'updated_at'
                ]);

                return $book;
            });

        return response()->json([
            'status' => true,
            'message' => 'Get Data Success!',
            'data' => $books->makeHidden([
                'id_author',
                'id_category',
                'created_at',
                'updated_at'
            ])
        ]);
    }
}
