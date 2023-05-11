<?php

namespace RicardoLobo\LaravelModelReviews\Commands;

use Illuminate\Console\Command;

class LaravelModelReviewsCommand extends Command
{
    public $signature = 'laravel-model-reviews';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
