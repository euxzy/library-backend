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

        $validated['photo'] = $request->getSchemeAndHttpHost() . '/storage/' . 'images/no_image.png';

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

    public function update(Request $request, $id)
    {
        $author = Author::query()->find($id);

        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

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
                'message' => 'Update author failed!'
            ]);
        }

        $validated = $validator->validated();

        if ($request->hasFile('photo')) {
            $oldPhoto = Str::of($author->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
            if (Storage::disk('public')->exists($oldPhoto)) {
                Storage::disk('public')->delete($oldPhoto);
            }
            $photo = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('photo')->store('images', 'public');
            $validated['photo'] = $photo;
        }

        $author->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'Update Author Success!'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $author = Author::query()->find($id);

        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => '404 Not Found!'
            ]);
        }

        $oldPhoto = Str::of($author->photo)->remove($request->getSchemeAndHttpHost() . '/storage');
        if (Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $author->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delete Data Author Success!'
        ]);
    }

    public function index()
    {
        $authors = Author::query()->get();

        return response()->json([
            'status' => true,
            'message' => 'Get Data Success!',
            'data' => $authors->makeHidden([
                'created_at',
                'updated_at'
            ])
        ]);
    }

    public function show($id)
    {
        $author = Author::query()->find($id);

        return response()->json([
            'status' => true,
            'message' => 'Get Data Success!',
            'data' => $author->makeHidden([
                'created_at',
                'updated_at'
            ])
        ]);
    }
}
