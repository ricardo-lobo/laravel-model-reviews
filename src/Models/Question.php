<?php

namespace RicardoLobo\LaravelModelReviews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'active',
    ];

    public function getTable()
    {
        return config('model-reviews.questions.table');
    }
}
