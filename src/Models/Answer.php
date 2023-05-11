<?php

namespace RicardoLobo\LaravelModelReviews\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;

class Answer extends Pivot
{
    public $incrementing = true;

    protected $fillable = [
        'rating',
        'question_id',
        'review_id',
    ];

    protected $casts = [
        'rating' => RatingEnum::class,
    ];

    public function getTable()
    {
        return config('model-reviews.answers.table');
    }
}
