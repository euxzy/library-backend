<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
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

        /* jika terdapat input gambar, maka gambar akan disimpan
        ke storage dan value photo akan diubah menjadi url
        dari gambar yang diinputkan */
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
}
