<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Exception;

class FetchBlogNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news from RSS feeds and generate blog posts using AI';

    public function handle()
    {
        $this->info('Starting to fetch and process news...');

        // Fetch settings
        $apiKey = Setting::where('key', 'avalai_api_key')->value('value');
        $baseUrl = Setting::where('key', 'avalai_base_url')->value('value') ?? 'https://api.avalai.ir/v1';
        $prompt = Setting::where('key', 'autopilot_prompt')->value('value');
        $categoryId = Setting::where('key', 'autopilot_category_id')->value('value');
        $rssFeedsText = Setting::where('key', 'autopilot_rss_feeds')->value('value');
        $minPosts = (int)(Setting::where('key', 'autopilot_min_posts_per_day')->value('value') ?? 1);
        $maxPosts = (int)(Setting::where('key', 'autopilot_max_posts_per_day')->value('value') ?? 3);
        $maxArticleAgeDays = (int)(Setting::where('key', 'autopilot_max_article_age_days')->value('value') ?? 3);
        $maxSearchDepth = (int)(Setting::where('key', 'autopilot_max_rss_items_to_check')->value('value') ?? 5);

        if (!$apiKey || !$prompt || !$categoryId || !$rssFeedsText) {
            $this->error('Missing required settings. Please configure Autopilot Settings in the admin panel.');
            return;
        }

        // Check how many auto-generated posts were already created today
        $todayPostsCount = Post::whereNotNull('source_url')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayPostsCount >= $maxPosts) {
            $this->info("Daily limit reached ({$maxPosts} posts). Skipping fetch.");
            return;
        }

        $postsNeeded = max($minPosts - $todayPostsCount, 1);
        $this->info("Targeting to fetch {$postsNeeded} new posts (Max allowed: {$maxPosts}, Created today: {$todayPostsCount}).");

        $feeds = array_filter(array_map('trim', explode("\n", $rssFeedsText)));
        
        $postsCreatedThisRun = 0;

        foreach ($feeds as $feedUrl) {
            if ($todayPostsCount + $postsCreatedThisRun >= $maxPosts) {
                $this->info("Maximum daily post limit ({$maxPosts}) reached during this run. Stopping.");
                break;
            }
            $this->info("Fetching RSS from: {$feedUrl}");
            
            try {
                $response = Http::get($feedUrl);
                if (!$response->successful()) {
                    $this->warn("Failed to fetch {$feedUrl}");
                    continue;
                }

                $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml || !isset($xml->channel->item)) {
                    $this->warn("Invalid RSS format for {$feedUrl}");
                    continue;
                }

                // Register media namespace if available
                $namespaces = $xml->getNamespaces(true);

                $items = $xml->channel->item;
                $processedCount = 0;
                $searchDepth = 0; // Prevent looking endlessly into old posts

                foreach ($items as $item) {
                    if ($searchDepth >= $maxSearchDepth) {
                        $this->info("Reached maximum search depth of {$maxSearchDepth} items for this feed. Moving to next feed.");
                        break;
                    }
                    $searchDepth++;

                    if ($todayPostsCount + $postsCreatedThisRun >= $maxPosts) {
                        $this->info("Maximum daily limit reached during processing. Stopping.");
                        break 2; // Break out of both loops if overall max is reached
                    }

                    // Only process up to max needed per feed to avoid rate limits
                    if ($processedCount >= $postsNeeded) {
                        $this->info("Reached target fetch count for this run. Moving to next feed or stopping.");
                        break;
                    }

                    $sourceUrl = (string) $item->link;
                    
                    // Check if already processed (either published or rejected by AI)
                    if (Post::where('source_url', $sourceUrl)->exists() || Cache::has('ai_rejected_' . md5($sourceUrl))) {
                        // Keep checking other items in the RSS feed instead of stopping
                        continue;
                    }

                    $title = (string) $item->title;
                    $description = (string) $item->description;
                    $content = isset($item->children('content', true)->encoded) 
                        ? (string) $item->children('content', true)->encoded 
                        : $description;
                    
                    // Check article age
                    $pubDateStr = (string) $item->pubDate;
                    if ($pubDateStr) {
                        try {
                            $pubDate = \Carbon\Carbon::parse($pubDateStr);
                            if ($pubDate->diffInDays(now()) > $maxArticleAgeDays) {
                                $this->info("Article '{$title}' is older than {$maxArticleAgeDays} days. Skipping...");
                                continue;
                            }
                        } catch (\Exception $e) {
                            // If we can't parse the date, continue anyway
                        }
                    }

                    // Try to extract image
                    $imageUrl = null;
                    
                    // 1. Try media:content
                    if (isset($namespaces['media'])) {
                        $media = $item->children($namespaces['media']);
                        if (isset($media->content) && isset($media->content->attributes()->url)) {
                            $imageUrl = (string) $media->content->attributes()->url;
                        }
                    }
                    
                    // 2. Try enclosure
                    if (!$imageUrl && isset($item->enclosure) && isset($item->enclosure->attributes()->url)) {
                        $type = (string) $item->enclosure->attributes()->type;
                        if (str_starts_with($type, 'image/')) {
                            $imageUrl = (string) $item->enclosure->attributes()->url;
                        }
                    }
                    
                    // 3. Try to extract from content HTML
                    if (!$imageUrl && preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches)) {
                        $imageUrl = $matches[1];
                    }
                    if (!$imageUrl && preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $description, $matches)) {
                        $imageUrl = $matches[1];
                    }

                    // 4. Try to fetch og:image from the source URL directly if no image found yet
                    if (!$imageUrl) {
                        try {
                            $pageContent = Http::timeout(5)->get($sourceUrl)->body();
                            if (preg_match('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"][^>]*>/i', $pageContent, $matches) || 
                                preg_match('/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]og:image[\'"][^>]*>/i', $pageContent, $matches)) {
                                $imageUrl = $matches[1];
                            }
                        } catch (\Exception $e) {
                            // Ignore errors during og:image extraction
                        }
                    }

                    // Clean tags for the prompt
                    $cleanContent = strip_tags($content);
                    $originalText = "Title: {$title}\n\nContent: {$cleanContent}";

                    $this->info("Sending article to AI for evaluation: {$title}");

                    $aiResult = $this->rewriteWithAI($apiKey, $baseUrl, $prompt, $originalText);

                    if (!$aiResult) {
                        $this->warn("Failed to parse AI JSON response for: {$title}");
                        continue;
                    }

                    // Gatekeeper check
                    if (isset($aiResult['is_relevant']) && $aiResult['is_relevant'] === false) {
                        $this->warn("AI rejected this article (Irrelevant). Saving to cache to ignore next time. Skipping...");
                        // Remember this rejection for 30 days so we don't ask AI again
                        Cache::put('ai_rejected_' . md5($sourceUrl), true, now()->addDays(30));
                        continue; // This skips the current item and moves to the next news item in the RSS loop
                    }

                    if (empty($aiResult['title']) || empty($aiResult['content'])) {
                        $this->error("AI marked as relevant but returned empty content for: {$title}");
                        continue;
                    }

                    // Try to download image if AI accepted the article
                    $imagePath = null;
                    if ($imageUrl) {
                        try {
                            $imageContents = Http::get($imageUrl)->body();
                            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                            $extension = $extension ?: 'jpg'; // Default to jpg if no extension found
                            $filename = 'posts/' . Str::random(40) . '.' . $extension;
                            
                            Storage::disk('public')->put($filename, $imageContents);
                            $imagePath = $filename;
                        } catch (\Exception $e) {
                            $this->warn("Failed to download image for {$aiResult['title']}: " . $e->getMessage());
                        }
                    }

                    // Generate a shorter, cleaner slug
                    // Take the first 5 words of the title, convert to slug, and append a short random string
                    $words = explode(' ', $aiResult['title']);
                    $shortTitle = implode(' ', array_slice($words, 0, 5));
                    $slug = Str::slug($shortTitle) . '-' . substr(uniqid(), -5);

                    Post::create([
                        'title' => $aiResult['title'],
                        'slug' => $slug,
                        'summary' => $aiResult['meta_description'] ?? Str::limit(strip_tags($aiResult['content']), 150),
                        'content' => $aiResult['content'],
                        'image_path' => $imagePath,
                        'category_id' => $categoryId,
                        'is_published' => true,
                        'published_at' => now(),
                        'source_url' => $sourceUrl,
                    ]);
                    $this->info("Successfully published relevant article: {$aiResult['title']}");
                    $processedCount++;
                    $postsCreatedThisRun++;

                    // Delay to prevent hitting rate limits
                    sleep(2);
                }

            } catch (Exception $e) {
                $this->error("Error processing {$feedUrl}: " . $e->getMessage());
            }
        }

        $this->info('News fetch completed.');
    }

    private function rewriteWithAI($apiKey, $baseUrl, $prompt, $content)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post(rtrim($baseUrl, '/') . '/chat/completions', [
            'model' => 'gpt-4o-mini', // or 'claude-3-haiku' depending on avalai availability
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that rewrites articles. You MUST return your answer in valid JSON format only, with no markdown code blocks outside the JSON. Format: {"title": "string", "summary": "string", "content": "html string"}'],
                ['role' => 'user', 'content' => $prompt . "\n\n" . $content]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiText = $data['choices'][0]['message']['content'] ?? '';
            
            // Clean up any potential markdown code blocks returned despite instructions
            $aiText = str_replace(['```json', '```'], '', $aiText);
            
            return json_decode(trim($aiText), true);
        }

        return null;
    }
}
