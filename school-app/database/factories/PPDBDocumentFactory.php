<?php
namespace Database\Factories;

use App\Models\{PPDBDocument, PPDBApplication};
use Illuminate\Database\Eloquent\Factories\Factory;

class PPDBDocumentFactory extends Factory
{
    protected $model = PPDBDocument::class;
    public function definition(): array
    {
        $app = PPDBApplication::factory()->create();
        return [
            'ppdb_application_id' => $app->id,
            'type' => $this->faker->randomElement(['kk','akta','rapor']),
            'file_path' => 'ppdb/'.date('Y/m/d').'/'.$this->faker->uuid().'.pdf',
            'verified_at' => null,
        ];
    }
}

