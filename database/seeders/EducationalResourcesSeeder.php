<?php

namespace Database\Seeders;

use App\Models\ExamType;
use App\Models\ResourceCategory;
use Illuminate\Database\Seeder;

class EducationalResourcesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // ایجاد نوع آزمون: نظام مهندسی
        $nazamMohandesi = ExamType::create([
            'title' => 'نظام مهندسی',
            'slug' => 'nazam-mohandesi',
            'description' => 'منابع آموزشی آزمون نظام مهندسی',
            'icon' => 'academic-cap',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // ایجاد دسته‌بندی ویدیو
        ResourceCategory::create([
            'exam_type_id' => $nazamMohandesi->id,
            'type' => 'video',
            'title' => 'ویدیوهای آموزشی',
            'slug' => 'nazam-mohandesi-videos',
            'description' => 'ویدیوهای آموزشی آزمون نظام مهندسی',
            'icon' => 'play-circle',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // ایجاد دسته‌بندی جزوات
        ResourceCategory::create([
            'exam_type_id' => $nazamMohandesi->id,
            'type' => 'document',
            'title' => 'جزوات آموزشی',
            'slug' => 'nazam-mohandesi-documents',
            'description' => 'جزوات و فایل‌های آموزشی آزمون نظام مهندسی',
            'icon' => 'document-text',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }
}
