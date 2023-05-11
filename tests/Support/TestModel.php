<?php

namespace RicardoLobo\LaravelModelReviews\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use RicardoLobo\LaravelModelReviews\Concerns\Reviewable;
use RicardoLobo\LaravelModelReviews\Traits\HasReviews;

class TestModel extends Model implements Reviewable
{
    use HasReviews;

    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'test_models';
}
