<?php

namespace App\Http\Controllers;

use App\Models\LearningFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningFileController extends Controller
{
    public function index()
    {
        return response()->json(LearningFile::with('uploader')->paginate(25));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LearningFile::class);
        $data = $request->validate([
            'title' => 'required|string',
            'file' => 'required|file',
            'subject_id' => 'nullable|exists:subjects,id',
            'agenda_id' => 'nullable|exists:agendas,id',
            'class_id' => 'nullable|exists:classes,id',
            'visibility' => 'nullable|in:public,private',
        ]);

        $file = $request->file('file');
        $path = $file->store('learning_files', 'public');

        $lf = LearningFile::create([
            'title' => $data['title'],
            'original_name' => $file->getClientOriginalName(),
            'filename' => basename($path),
            'path' => $path,
            'mimetype' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'subject_id' => $data['subject_id'] ?? null,
            'agenda_id' => $data['agenda_id'] ?? null,
            'class_id' => $data['class_id'] ?? null,
            'visibility' => $data['visibility'] ?? 'private',
        ]);

        return response()->json($lf, 201);
    }

    public function show(LearningFile $learningFile)
    {
        return response()->json($learningFile);
    }

    public function download(LearningFile $learningFile)
    {
        return Storage::disk('public')->download($learningFile->path, $learningFile->original_name);
    }

    public function destroy(LearningFile $learningFile)
    {
        $this->authorize('delete', $learningFile);
        Storage::disk('public')->delete($learningFile->path);
        $learningFile->delete();
        return response()->json(['deleted' => true]);
    }
}
