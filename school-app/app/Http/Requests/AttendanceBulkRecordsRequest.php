<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceBulkRecordsRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'records' => 'required|array|min:1',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:present,late,excused,absent',
            'records.*.note' => 'nullable|string|max:255',
        ];
    }
}

