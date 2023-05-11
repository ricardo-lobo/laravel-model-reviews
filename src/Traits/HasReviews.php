<?php

namespace RicardoLobo\LaravelModelReviews\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Client\Request;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewFactoryInterface;
use RicardoLobo\LaravelModelReviews\Http\Requests\CreateReviewFormRequest;

/**
 * @mixin Model
 */
trait HasReviews
{
    public function review(): MorphOne
    {
        return $this->morphOne(config('model-reviews.reviews.model'), 'reviewable')->latestOfMany();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(
            related: config('model-reviews.reviews.model'),
            name:'reviewable',
        );
    }

    public function createReview(array $data): Model
    {
        app(ReviewFactoryInterface::class)->create($this, $data);

        return $this;
    }

    public function createReviewFromRequest(): Model
    {
        $form = app(CreateReviewFormRequest::class);

        app(ReviewFactoryInterface::class)->create($this, $form->validated('review'));

        return $this;
    }
}
