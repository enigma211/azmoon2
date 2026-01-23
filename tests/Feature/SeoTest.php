<?php

use App\Models\Exam;
use App\Models\ExamDomain;
use App\Models\ExamBatch;
use Illuminate\Support\Facades\Http;

test('robots.txt exists in public directory and contains sitemap link', function () {
    $path = public_path('robots.txt');
    
    expect(file_exists($path))->toBeTrue();
    
    $content = file_get_contents($path);
    expect($content)->toContain('User-agent: *');
    expect($content)->toContain('Sitemap:');
});

test('sitemap.xml is accessible and returns valid xml', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    $response->assertSee('<urlset', false);
    $response->assertSee('</urlset>', false);
});

test('home page has essential meta tags', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // These are generic SEO tags, adjust based on your actual blade files
    $response->assertSee('<title>', false);
    $response->assertSee('description', false);
});

test('exam landing pages are public for seo', function () {
    $domain = ExamDomain::create([
        'title' => 'Test Domain',
        'slug' => 'test-domain'
    ]);
    $batch = ExamBatch::create([
        'exam_domain_id' => $domain->id,
        'title' => 'Test Batch',
        'slug' => 'test-batch'
    ]);
    
    $exam = Exam::create([
        'exam_batch_id' => $batch->id,
        'title' => 'Test SEO Exam',
        'slug' => 'test-seo-exam',
        'description' => 'SEO Test Description',
        'duration_minutes' => 60,
        'pass_threshold' => 50,
        'is_published' => true,
    ]);

    $response = $this->get(route('exam.landing', ['exam' => $exam->id]));

    $response->assertStatus(200);
    $response->assertSee($exam->title);
});

test('blog index is public and accessible', function () {
    $response = $this->get(route('blog.index'));

    $response->assertStatus(200);
});
