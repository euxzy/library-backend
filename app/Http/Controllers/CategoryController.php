<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
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

        // mengambil hasil validasi request yang diinput
        $validated = $validator->validated();

        // jika tidak ada gambar yang diinputkan, maka default url akan diubah
        $validated['logo'] = $request->getSchemeAndHttpHost() . '/storage/' . 'images/logo.png';

        /* jika terdapat input gambar, maka gambar akan disimpan
        ke storage dan value photo akan diubah menjadi url
        dari gambar yang diinputkan */
        if ($request->hasFile('logo')) {
            $photo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('logo')->store('images', 'public');
            $validated['photo'] = $photo;
        }

        Category::create($validated); // menambahkan data ke database
        return response()->json([
            'status' => true,
            'message' => 'Add category success!',
            'data' => $validated
        ]);
    }
}
