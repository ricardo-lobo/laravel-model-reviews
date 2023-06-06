<?php

namespace RicardoLobo\LaravelModelReviews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $reviewable_id
 * @property string $reviewable_type
 * @property int $author_id
 * @property string $author_type
 * @property string $comment
 */
class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'author_id',
        'author_type',
        'comment',
    ];

    public function getTable()
    {
        return config('model-reviews.reviews.table');
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function questions(): BelongsToMany
    {
        return $this
            ->belongsToMany(config('model-reviews.questions.model'), config('model-reviews.answers.table'))
            ->using(Answer::class)
            ->withTimestamps();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(config('model-reviews.answers.model'), 'review_id');
    }
}
