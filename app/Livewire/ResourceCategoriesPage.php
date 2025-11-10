<?php

namespace App\Livewire;

use App\Models\ExamType;
use Livewire\Component;

class ResourceCategoriesPage extends Component
{
    public $examType;

    public function mount($slug)
    {
        $this->examType = ExamType::where('slug', $slug)
            ->where('is_active', true)
            ->with(['resourceCategories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.resource-categories-page')->layout('layouts.app', [
            'seoTitle' => 'منابع آموزشی ' . $this->examType->title . ' - آزمون کده',
            'seoDescription' => $this->examType->description ?: 'دسترسی به ویدیوها و جزوات آموزشی ' . $this->examType->title,
        ]);
    }
}
