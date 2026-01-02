<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LecturerController extends Controller
{
    public function index()
    {
        return response()->json(Lecturer::with('user')->paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nidn' => 'nullable|string|unique:lecturers,nidn',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo' => 'nullable|file|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('lecturers', 'public');
            $data['photo'] = $path;
        }

        $lecturer = Lecturer::create($data);

        return response()->json($lecturer, 201);
    }

    public function show(Lecturer $lecturer)
    {
        return response()->json($lecturer->load('user'));
    }

    public function update(Request $request, Lecturer $lecturer)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nidn' => 'nullable|string|unique:lecturers,nidn,'.$lecturer->id,
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'photo' => 'nullable|file|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) {
                Storage::disk('public')->delete($lecturer->photo);
            }
            $data['photo'] = $request->file('photo')->store('lecturers', 'public');
        }

        $lecturer->update($data);

        return response()->json($lecturer);
    }

    public function destroy(Lecturer $lecturer)
    {
        $lecturer->delete();
        return response()->json(['deleted' => true]);
    }
}
