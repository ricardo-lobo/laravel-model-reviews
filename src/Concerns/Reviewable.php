<?php

namespace RicardoLobo\LaravelModelReviews\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Reviewable
{
    public function review(): MorphOne;

    public function reviews(): MorphMany;

    public function createReview(array $data): Model;

    public function createReviewFromRequest(): Model;

    public function getReviewAuthor(): ?Model;
}
