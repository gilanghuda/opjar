<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(Subject::paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:subjects,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:0',
        ]);

        $subject = Subject::create($data);

        return response()->json($subject, 201);
    }

    public function show(Subject $subject)
    {
        return response()->json($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:subjects,code,'.$subject->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:0',
        ]);

        $subject->update($data);

        return response()->json($subject);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['deleted' => true]);
    }
}
