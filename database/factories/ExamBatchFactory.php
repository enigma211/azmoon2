<?php

namespace Database\Factories;

use App\Models\ExamBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamBatchFactory extends Factory
{
    protected $model = ExamBatch::class;

    public function definition(): array
    {
        return [
            'exam_domain_id' => \App\Models\ExamDomain::factory(),
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug,
            'is_free' => false,
        ];
    }
}
