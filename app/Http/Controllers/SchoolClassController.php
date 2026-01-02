<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        return response()->json(SchoolClass::paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:classes,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $c = SchoolClass::create($data);

        return response()->json($c, 201);
    }

    public function show(SchoolClass $class)
    {
        return response()->json($class);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:classes,code,'.$class->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $class->update($data);

        return response()->json($class);
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return response()->json(['deleted' => true]);
    }
}
