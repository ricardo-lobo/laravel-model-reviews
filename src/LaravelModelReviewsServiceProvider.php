<?php

namespace RicardoLobo\LaravelModelReviews;

use Illuminate\Foundation\Application;
use RicardoLobo\LaravelModelReviews\Commands\LaravelModelReviewsCommand;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewAuthorResolverInterface;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewFactoryInterface;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelModelReviewsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-reviews')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_model_reviews_table',
                'create_questions_table',
            ])
            ->hasCommand(LaravelModelReviewsCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->bind(ReviewFactoryInterface::class, fn (Application $app) => $app->make(config('model-reviews.factories.review')));

        $this->app->scoped(ReviewAuthorResolverInterface::class, fn (Application $app) => $app->make(config('model-reviews.author.resolver')));
    }
}
