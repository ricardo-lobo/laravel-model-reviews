<?php

namespace RicardoLobo\LaravelModelReviews\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;

/**
 * @property int $id
 * @property int $rating
 * @property string $question_title
 * @property int $question_id
 * @property int $review_id
 */
class Answer extends Pivot
{
    public $incrementing = true;

    protected $fillable = [
        'rating',
        'question_title',
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
