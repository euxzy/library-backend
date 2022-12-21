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

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Add author failed!'
            ]);
        }

        $validated = $validator->validated();
        $validated['photo'] = 'images/authors/no_image.png';
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
