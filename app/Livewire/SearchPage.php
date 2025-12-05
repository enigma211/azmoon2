<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Question;
use App\Models\ExamDomain;
use Illuminate\Database\Eloquent\Builder;

class SearchPage extends Component
{
    #[Url(as: 'q')]
    public $query = '';

    #[Url]
    public $domain = '';

    public function search()
    {
        // Just to trigger re-render with new URL params
    }

    public function render()
    {
        $domains = ExamDomain::where('is_active', true)->get();
        
        $results = collect();

        if (strlen($this->query) >= 2) {
            $q = Question::query()
                ->where('is_deleted', false)
                ->with(['exam.batch.domain']);

            // Filter by domain if selected
            if ($this->domain) {
                $q->whereHas('exam.batch', function (Builder $b) {
                    $b->where('exam_domain_id', $this->domain);
                });
            }

            // Search logic
            $q->where(function($subQ) {
                // 1. Robust LIKE search: Replace spaces with % to handle ZWNJ (half-space) and word distance
                // e.g. "پی سازی" will match "پی سازی", "پی‌سازی" (ZWNJ), "پی و سازی"
                $searchTerms = explode(' ', trim($this->query));
                $likeQuery = '%' . implode('%', $searchTerms) . '%';
                
                $subQ->where('text', 'LIKE', $likeQuery);

                // 2. Boolean FullText search (stricter than Natural Language)
                // We prepend '+' to each word to enforce AND logic (must contain all words)
                $booleanQuery = '';
                foreach ($searchTerms as $term) {
                    if (mb_strlen($term) > 1) { 
                        $booleanQuery .= '+' . $term . ' ';
                    }
                }
                $booleanQuery = trim($booleanQuery);

                if (!empty($booleanQuery)) {
                    // Use Raw for Boolean Mode
                    $subQ->orWhereRaw("MATCH(text) AGAINST(? IN BOOLEAN MODE)", [$booleanQuery]);
                }
            });
            
            // Order by relevance: Exact/LIKE matches first, then others
            // We use a raw order clause. 
            // Note: In standard SQL, boolean result is 1/0. DESC puts 1 (match) first.
            // We reconstruct the like query for the ordering clause
            $likeQueryRaw = '%' . implode('%', explode(' ', trim($this->query))) . '%';
            $q->orderByRaw("CASE WHEN text LIKE ? THEN 1 ELSE 0 END DESC", [$likeQueryRaw]);

            $results = $q->latest()->limit(100)->get();
        }

        return view('livewire.search-page', [
            'domains' => $domains,
            'results' => $results
        ])->layout('layouts.app', [
            'seoTitle' => 'جستجوی سوالات - آزمون کده',
            'seoDescription' => 'جستجوی پیشرفته در بانک سوالات آزمون‌های نظام مهندسی و کارشناس رسمی',
        ]);
    }
}
