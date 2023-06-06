<?php

namespace RicardoLobo\LaravelModelReviews\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function review(): BelongsTo
    {
        return $this->belongsTo(config('model-reviews.reviews.model'));
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(config('model-reviews.questions.model'));
    }
}
