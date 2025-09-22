<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{AttendanceSession, AttendanceRecord, ClassSubject, Enrollment, GradeItem, Grade, Student};

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = auth()->user()->teacher->id ?? null;
        $query = \App\Models\AttendanceSession::query()
            ->where('teacher_id', $teacherId);
        if ($request->filled('class_subject_id')) $query->where('class_subject_id', $request->class_subject_id);
        if ($request->filled('date')) $query->whereDate('session_date', $request->date);
        return $query->latest('session_date')->paginate(20);
    }

    public function store(\App\Http\Requests\AttendanceSessionStoreRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = auth()->user()->teacher->id;
        $data['status'] = 'planned';
        $session = \App\Models\AttendanceSession::create($data);
        return response()->json($session, 201);
    }

    public function open($id)
    {
        $session = \App\Models\AttendanceSession::findOrFail($id);
        $this->authorize('update', $session);
        $session->update(['status' => 'open']);

        // preload records for enrolled students (absent by default)
        $enrolled = \App\Models\Enrollment::where('class_id', $session->class_id)->pluck('student_id');
        foreach ($enrolled as $sid) {
            \App\Models\AttendanceRecord::firstOrCreate(
                ['attendance_session_id' => $session->id, 'student_id' => $sid],
                ['status' => 'absent']
            );
        }
        return response()->json(['status'=>'opened']);
    }

    public function bulkRecords(\App\Http\Requests\AttendanceBulkRecordsRequest $request, $id)
    {
        $session = \App\Models\AttendanceSession::findOrFail($id);
        $this->authorize('update', $session);
        $payload = $request->validated();
        foreach ($payload['records'] as $r) {
            \App\Models\AttendanceRecord::updateOrCreate(
                ['attendance_session_id' => $session->id, 'student_id' => $r['student_id']],
                ['status' => $r['status'], 'note' => $r['note'] ?? null, 'checkin_at' => now()]
            );
        }
        return response()->json(['updated'=>count($payload['records'])]);
    }

    public function close($id)
    {
        $session = \App\Models\AttendanceSession::findOrFail($id);
        $this->authorize('update', $session);
        $session->update(['status'=>'closed']);
        event(new \App\Events\AttendanceSessionClosed($session));
        return response()->json(['status'=>'closed', 'event'=>'AttendanceSessionClosed']);
    }
}
