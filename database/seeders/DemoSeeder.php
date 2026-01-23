<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\ExamDomain;
use App\Models\ExamBatch;
use App\Models\Exam;
use App\Models\ExamSection;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ResourceItem;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Domain
        $domain = ExamDomain::firstOrCreate(
            ['slug' => 'nezam-mohandesi'],
            [
                'title' => 'نظام مهندسی',
                'is_active' => true,
            ]
        );

        // 2) Batch
        $batch = ExamBatch::firstOrCreate(
            [
                'exam_domain_id' => $domain->id,
                'slug' => 'khordad-1404',
            ],
            [
                'title' => 'خرداد ۱۴۰۴',
                'is_active' => true,
            ]
        );

        // 3) Exam
        $exam = Exam::firstOrCreate(
            [
                'exam_batch_id' => $batch->id,
                'slug' => 'demo-10-questions',
            ],
            [
                'title' => 'آزمون دمو ۱۰ سوالی',
                'description' => 'آزمون نمونه با انواع سوال‌ها برای آزمایش سیستم.',
                'duration_minutes' => 20,
                'pass_threshold' => 60.0,
                'is_published' => true,
            ]
        );

        // 4) Questions
        // Helper to create questions succinctly
        $makeQuestion = function(array $attrs) use ($exam) {
            $q = Question::create(array_merge([
                'exam_id' => $exam->id,
                'difficulty' => 'easy',
                'score' => 1,
            ], $attrs));
            return $q;
        };

        // 5) Create 10 questions (mixed types)
        // Single choice x3
        for ($i = 1; $i <= 3; $i++) {
            $q = $makeQuestion([
                'type' => 'single_choice',
                'text' => "سوال تک‌گزینه‌ای شماره {$i}",
                'negative_score' => 0,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه ۱', 'is_correct' => false, 'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه ۲', 'is_correct' => true,  'order' => 2]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه ۳', 'is_correct' => false, 'order' => 3]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه ۴', 'is_correct' => false, 'order' => 4]);
        }

        // Multi choice x3
        for ($i = 1; $i <= 3; $i++) {
            $q = $makeQuestion([
                'type' => 'multi_choice',
                'text' => "سوال چندگزینه‌ای شماره {$i}",
                'negative_score' => 0.25,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه A', 'is_correct' => true,  'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه B', 'is_correct' => true,  'order' => 2]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه C', 'is_correct' => false, 'order' => 3]);
            Choice::create(['question_id' => $q->id, 'text' => 'گزینه D', 'is_correct' => false, 'order' => 4]);
        }

        // True/False x2
        for ($i = 1; $i <= 2; $i++) {
            $q = $makeQuestion([
                'type' => 'true_false',
                'text' => "سوال صحیح/غلط شماره {$i}",
                'negative_score' => 0,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'صحیح', 'is_correct' => ($i % 2 === 1), 'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'غلط',  'is_correct' => ($i % 2 === 0), 'order' => 2]);
        }

        // Short answer x2 (use explanation as answer key for demo)
        for ($i = 1; $i <= 2; $i++) {
            $makeQuestion([
                'type' => 'short_answer',
                'text' => "سوال پاسخ کوتاه شماره {$i} (کلید: کلمه‌کلیدی{$i})",
                'explanation' => "کلمه‌کلیدی{$i}",
                'negative_score' => 0,
            ]);
        }

        // 6) Resource PDFs (external URLs for demo)
        $pdfs = [
            ['title' => 'راهنمای مطالعه 1', 'url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
            ['title' => 'راهنمای مطالعه 2', 'url' => 'https://www.adobe.com/support/products/enterprise/knowledgecenter/media/c4611_sample_explain.pdf'],
        ];
        foreach ($pdfs as $p) {
            ResourceItem::create([
                'type' => 'pdf',
                'title' => $p['title'],
                'description' => 'منبع آموزشی دمو',
                'file_path' => $p['url'],
                'exam_domain_id' => $domain->id,
                'exam_batch_id' => $batch->id,
                'exam_id' => $exam->id,
            ]);
        }

        // 7) Subscription plan demo
        $plan = SubscriptionPlan::firstOrCreate(
            ['title' => 'پلن ۳ ماهه'],
            [
                'description' => 'دسترسی کامل (all)',
                'price_toman' => 200000,
                'duration_days' => 90,
                'access_scope' => 'all',
            ]
        );

        // 8) Demo student user with active subscription
        $user = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'دانشجو دمو',
                'password' => Hash::make('password123'),
            ]
        );

        UserSubscription::firstOrCreate(
            [
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
            ],
            [
                'starts_at' => Carbon::now()->subDay(),
                'ends_at' => Carbon::now()->addMonths(3),
                'status' => 'active',
            ]
        );
    }
}
