<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return response()->json(Attendance::with(['agenda','user','recorder'])->paginate(25));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,permission,late,excused',
            'remarks' => 'nullable|string',
        ]);

        $data['recorded_by'] = $request->user()->id;

        $att = Attendance::create($data);

        // mark agenda attendance taken flag
        $att->agenda()->update(['attendance_taken' => true]);

        return response()->json($att, 201);
    }

    public function show(Attendance $attendance)
    {
        return response()->json($attendance);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => 'nullable|in:present,absent,permission,late,excused',
            'remarks' => 'nullable|string',
        ]);

        $attendance->update($data);

        return response()->json($attendance);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['deleted' => true]);
    }
}
