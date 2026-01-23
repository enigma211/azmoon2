<?php

namespace Database\Factories;

use App\Models\ExamDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamDomainFactory extends Factory
{
    protected $model = ExamDomain::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word,
            'slug' => $this->faker->slug,
            'is_active' => true,
        ];
    }
}
