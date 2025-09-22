<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceSessionStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'class_id' => 'nullable|exists:classes,id',
            'class_subject_id' => 'nullable|exists:class_subjects,id',
            'term_id' => 'nullable|exists:terms,id',
            'session_date' => 'required|date',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'topic' => 'nullable|string|max:191',
        ];
    }
}

