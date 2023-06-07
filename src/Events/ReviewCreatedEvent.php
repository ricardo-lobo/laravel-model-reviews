<?php

namespace RicardoLobo\LaravelModelReviews\Events;

use Illuminate\Database\Eloquent\Model;
use RicardoLobo\LaravelModelReviews\Models\Review;

class ReviewCreatedEvent
{
    public function __construct(public Model|Review $review)
    {
    }
}
