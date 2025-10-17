<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_domain_id',
        'title',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(ExamDomain::class, 'exam_domain_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
