<?php

namespace RicardoLobo\LaravelModelReviews\Concerns;

interface ReviewFactoryInterface
{
    public function create(Reviewable $reviewable, array $answers, string $comment = null): void;
}
