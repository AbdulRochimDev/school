<?php
namespace Database\Factories;

use App\Models\{SubmissionFile, Submission};
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFileFactory extends Factory
{
    protected $model = SubmissionFile::class;
    public function definition(): array
    {
        $submission = Submission::factory()->create();
        return [
            'submission_id' => $submission->id,
            'file_path' => 'submissions/'.date('Y/m/d').'/'.$this->faker->uuid().'.txt',
            'mime_type' => 'text/plain',
            'size_bytes' => $this->faker->numberBetween(100, 5000),
        ];
    }
}

