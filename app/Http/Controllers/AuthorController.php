<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function store(Request $request)
    {
        // validasi request yang diinputkan
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'gender' => 'required',
            'birth_date' => 'required',
            'religion' => 'required:20',
            'ethnic' => 'required:20',
            'citizenship' => 'required|max:30',
            'photo' => 'image|max:2048',
            'hobbies' => 'required',
            'description' => 'required'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Add author failed!'
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

        Author::create($validated);
        return response()->json([
            'status' => true,
            'message' => 'Add author success!',
            'data' => $validated
        ]);
    }
}
