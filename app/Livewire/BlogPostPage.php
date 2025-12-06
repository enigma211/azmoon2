<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Str;

class BlogPostPage extends Component
{
    public $slug;
    public Post $post;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->post = Post::published()->where('slug', $slug)->firstOrFail();
        
        // Increment view count
        $this->post->increment('view_count');
    }

    public function render()
    {
        $recentPosts = Post::published()
            ->where('id', '!=', $this->post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.blog-post-page', [
            'recentPosts' => $recentPosts
        ])->layout('layouts.app', [
            'seoTitle' => $this->post->title . ' - آزمون کده',
            'seoDescription' => $this->post->summary ?? Str::limit(strip_tags($this->post->content), 160),
            'seoKeywords' => $this->post->meta_keywords
        ]);
    }
}
