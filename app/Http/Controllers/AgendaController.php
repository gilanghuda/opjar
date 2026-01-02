<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Agenda::with(['lecturer.user','subject','schoolClass']);

        if ($request->user()->roles()->where('name','lecturer')->exists()) {
            // show only own agendas
            $lecturer = $request->user()->lecturer;
            if ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            }
        }

        return response()->json($query->paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'material' => 'nullable|string',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('agendas', 'public');
        }

        $data['created_by'] = $request->user()->id;

        $agenda = Agenda::create($data);

        return response()->json($agenda, 201);
    }

    public function show(Agenda $agenda)
    {
        return response()->json($agenda->load(['lecturer.user','subject','schoolClass','attendances','files']));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $this->authorize('update', $agenda);
        $data = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'material' => 'nullable|string',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            if ($agenda->photo) {
                Storage::disk('public')->delete($agenda->photo);
            }
            $data['photo'] = $request->file('photo')->store('agendas', 'public');
        }

        $agenda->update($data);

        return response()->json($agenda);
    }

    public function destroy(Agenda $agenda)
    {
        $this->authorize('delete', $agenda);
        $agenda->delete();
        return response()->json(['deleted' => true]);
    }
}
