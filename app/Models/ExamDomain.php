<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'is_active',
    ];

    public function batches()
    {
        return $this->hasMany(ExamBatch::class, 'exam_domain_id');
    }
}
