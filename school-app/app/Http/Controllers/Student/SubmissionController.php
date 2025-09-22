<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function store(\App\Http\Requests\SubmissionStoreRequest $request, $assignmentId)
    {
        $data = $request->validated();

        $studentId = auth()->user()->student->id;
        $submission = \App\Models\Submission::updateOrCreate(
            ['assignment_id'=>$assignmentId, 'student_id'=>$studentId],
            ['content'=>$data['content'] ?? null, 'submitted_at'=>now()]
        );

        if($request->hasFile('files')){
            foreach($request->file('files') as $uploaded){
                $path = $uploaded->store('submissions/'.date('Y/m/d'));
                \App\Models\SubmissionFile::create([
                    'submission_id'=>$submission->id,
                    'file_path'=>$path,
                    'mime_type'=>$uploaded->getClientMimeType(),
                    'size_bytes'=>$uploaded->getSize(),
                ]);
            }
        }
        event(new \App\Events\SubmissionReceived($submission));
        return response()->json($submission->load('files'), 201);
    }

    public function show($assignmentId)
    {
        $studentId = auth()->user()->student->id;
        $submission = \App\Models\Submission::with('files')
            ->where('assignment_id',$assignmentId)->where('student_id',$studentId)->first();
        abort_unless($submission, 404);
        return $submission;
    }
}
