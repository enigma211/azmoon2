<?php

namespace App\Livewire;

use App\Models\ExamType;
use App\Models\ResourceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ResourcePostsPage extends Component
{
    use WithPagination;

    public $examType;
    public $category;

    public function mount($examTypeSlug, $categorySlug)
    {
        $this->examType = ExamType::where('slug', $examTypeSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->category = ResourceCategory::where('slug', $categorySlug)
            ->where('exam_type_id', $this->examType->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        $posts = $this->category->activePosts()->paginate(12);

        return view('livewire.resource-posts-page', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'seoTitle' => $this->category->title . ' - ' . $this->examType->title . ' - آزمون کده',
            'seoDescription' => $this->category->description ?: 'مشاهده ' . $this->category->title . ' برای ' . $this->examType->title,
        ]);
    }
}
