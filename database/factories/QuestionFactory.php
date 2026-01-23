<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'type' => 'single_choice',
            'text' => $this->faker->paragraph,
            'is_deleted' => false,
        ];
    }
}
