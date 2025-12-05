<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Question;
use App\Models\Domain;
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
        $domains = Domain::where('is_active', true)->get();
        
        $results = collect();

        if (strlen($this->query) >= 2) {
            $q = Question::query()
                ->where('is_deleted', false)
                ->with(['exam.batch.domain']);

            // Filter by domain if selected
            if ($this->domain) {
                $q->whereHas('exam.batch', function (Builder $b) {
                    $b->where('domain_id', $this->domain);
                });
            }

            // Search logic: Try FullText first, fallback/combine with LIKE for better coverage of partial words
            $q->where(function($subQ) {
                // Use LIKE for broader matching especially with Persian characters
                $subQ->where('text', 'LIKE', '%' . $this->query . '%')
                     ->orWhereFullText('text', $this->query);
            });

            $results = $q->latest()->limit(100)->get();
        }

        return view('livewire.search-page', [
            'domains' => $domains,
            'results' => $results
        ]);
    }
}
