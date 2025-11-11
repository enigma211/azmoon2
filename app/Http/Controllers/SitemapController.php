<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamBatch;
use App\Models\ExamType;
use App\Models\ResourceCategory;
use App\Models\EducationalPost;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', 600, function () {
            $urls = [];

            $urls[] = [
                'loc' => route('educational-resources'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];

            $examTypes = ExamType::query()->where('is_active', true)->orderBy('id','asc')->get(['id','slug','updated_at']);
            foreach ($examTypes as $type) {
                $urls[] = [
                    'loc' => route('educational-resources.categories', $type->slug),
                    'lastmod' => optional($type->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            $categories = ResourceCategory::query()->where('is_active', true)->orderBy('id','asc')->get(['id','exam_type_id','slug','type','updated_at']);
            $typeMap = $examTypes->keyBy('id');
            foreach ($categories as $cat) {
                $type = $typeMap->get($cat->exam_type_id);
                if (!$type) continue;
                $urls[] = [
                    'loc' => route('educational-resources.posts', [$type->slug, $cat->slug]),
                    'lastmod' => optional($cat->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }

            $posts = EducationalPost::query()
                ->where('is_active', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('id','asc')
                ->get(['id','resource_category_id','slug','updated_at']);
            $catMap = $categories->keyBy('id');
            foreach ($posts as $post) {
                $cat = $catMap->get($post->resource_category_id);
                if (!$cat) continue;
                $type = $typeMap->get($cat->exam_type_id);
                if (!$type) continue;
                $urls[] = [
                    'loc' => route('educational-resources.post', [$type->slug, $cat->slug, $post->slug]),
                    'lastmod' => optional($post->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }

            $batches = ExamBatch::query()->orderBy('id','asc')->get(['id','updated_at']);
            foreach ($batches as $batch) {
                $urls[] = [
                    'loc' => route('exams', $batch),
                    'lastmod' => optional($batch->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }

            $exams = Exam::query()->orderBy('id','asc')->get(['id','updated_at']);
            foreach ($exams as $exam) {
                $urls[] = [
                    'loc' => route('exam.landing', ['exam' => $exam->id]),
                    'lastmod' => optional($exam->updated_at)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($urls as $u) {
                $xml .= '<url>';
                $xml .= '<loc>' . e($u['loc']) . '</loc>';
                if (!empty($u['lastmod'])) {
                    $xml .= '<lastmod>' . e($u['lastmod']) . '</lastmod>';
                }
                if (!empty($u['changefreq'])) {
                    $xml .= '<changefreq>' . e($u['changefreq']) . '</changefreq>';
                }
                if (!empty($u['priority'])) {
                    $xml .= '<priority>' . e($u['priority']) . '</priority>';
                }
                $xml .= '</url>';
            }
            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
