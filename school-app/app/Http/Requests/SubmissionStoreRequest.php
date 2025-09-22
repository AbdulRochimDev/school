<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'content' => 'nullable|string',
            'files.*' => 'file|max:10240',
        ];
    }
}

