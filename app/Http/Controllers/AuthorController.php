<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Support\Str;
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
            'religion' => 'required|max:20',
            'ethnic' => 'required|max:20',
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

        Author::create($validated); // menambahkan data ke database
        return response()->json([
            'status' => true,
            'message' => 'Add author success!',
            'data' => $validated
        ]);
    }

    public function update(Request $request, $id)
    {
        // mencari data author berdasarkan id
        $author = Author::query()->find($id);

        // cek ada atau tidak nya author di database
        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }


        // validasi request yang diinputkan
        $validator = Validator::make($request->all(), [
            'name' => 'max:50',
            'gender' => '',
            'birth_date' => 'date',
            'religion' => 'max:20',
            'ethnic' => 'max:20',
            'citizenship' => 'max:30',
            'photo' => 'image|max:2048',
            'hobbies' => 'min:5',
            'description' => 'min:5'
        ]);

        // jika validasi gagal maka akan return false
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Add author failed!'
            ]);
        }

        // mengambil hasil validasi request yang diinput
        $validated = $validator->getData();

        /* jika terdapat input gambar, maka gambar akan disimpan
        ke storage dan value photo akan diubah menjadi url
        dari gambar yang diinputkan */
        if ($request->hasFile('photo')) {
            // mendapatkan path file dari photo lama yang ada di database
            $oldPhoto = Str::of($author->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
            // cek apakah gambar ada di storage
            if (Storage::disk('public')->exists($oldPhoto)) {
                // jika ada, maka akan menghapus gambar lama
                Storage::disk('public')->delete($oldPhoto);
            }
            $photo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('photo')->store('images', 'public');
            $validated['photo'] = $photo;
        }

        $author->update($validated); // update data ke database
        return response()->json([
            'status' => true,
            'message' => 'Update Author Success!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // mencari data author berdasarkan id
        $author = Author::query()->find($id);

        // cek ada atau tidak nya author di database
        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        // mendapatkan path file dari photo lama yang ada di database
        $oldPhoto = Str::of($author->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
        // cek apakah gambar ada di storage
        if (Storage::disk('public')->exists($oldPhoto)) {
            // jika ada, maka akan menghapus gambar dari storage
            Storage::disk('public')->delete($oldPhoto);
        }

        // menghapus data author dari database
        $author->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delete Data Author Success!'
        ]);
    }
}
