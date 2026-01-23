<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        return [
            'exam_batch_id' => ExamBatch::factory(),
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug,
            'duration_minutes' => 30,
            'pass_threshold' => 50,
            'is_published' => true,
        ];
    }
}
