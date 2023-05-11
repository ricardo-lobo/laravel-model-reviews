<?php

namespace RicardoLobo\LaravelModelReviews\Concerns;

use Illuminate\Database\Eloquent\Model;

interface ReviewAuthorResolverInterface
{
    public function resolve(Model|int|string|null $subject = null): ?Model;
}
