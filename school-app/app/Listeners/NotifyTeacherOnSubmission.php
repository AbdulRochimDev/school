<?php
namespace App\Listeners;

use App\Events\SubmissionReceived;
use App\Models\Notification;

class NotifyTeacherOnSubmission
{
    public function handle(SubmissionReceived $event): void
    {
        $submission = $event->submission;
        $assignment = $submission->assignment;
        $teacher = optional(optional($assignment)->classSubject)->teacher;
        $userId = $teacher->user_id ?? null;
        if (!$userId) return;
        // Persist a simple notification row (implementation-neutral)
        Notification::create([
            'user_id' => $userId,
            'type' => 'submission.received',
            'data' => json_encode(['assignment_id' => $assignment?->id, 'submission_id' => $submission->id]),
            'read_at' => null,
        ]);
    }
}
