<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Assignment, Submission, Grade, GradeItem};

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $q = Assignment::query()->latest('id');
        if ($request->filled('class_subject_id')) $q->where('class_subject_id', $request->class_subject_id);
        return $q->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_subject_id' => 'required|exists:class_subjects,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
            'max_score' => 'nullable|numeric',
        ]);
        $assignment = Assignment::create($data);
        return response()->json($assignment, 201);
    }

    public function show($id)
    { return Assignment::findOrFail($id); }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|string|max:191',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
            'max_score' => 'nullable|numeric',
        ]);
        $assignment->update($data);
        return $assignment;
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        return response()->json(['deleted' => true]);
    }

    public function grade(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $payload = $request->validate([
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'required|numeric',
        ]);
        // Create/find GradeItem for this assignment
        $gi = GradeItem::firstOrCreate(
            ['class_subject_id' => $assignment->class_subject_id, 'name' => 'Assignment: '.$assignment->title],
            ['weight' => 10, 'max_score' => $assignment->max_score ?? 100]
        );
        $studentIds = [];
        foreach ($payload['grades'] as $g) {
            $studentIds[] = $g['student_id'];
            Grade::updateOrCreate(
                ['grade_item_id' => $gi->id, 'student_id' => $g['student_id']],
                ['score' => $g['score'], 'graded_at' => now()]
            );
        }
        event(new \App\Events\GradesUpdated(array_values(array_unique($studentIds))));
        return response()->json(['updated' => count($payload['grades'])]);
    }
}

