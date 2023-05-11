<?php

// config for RicardoLobo/LaravelModelReviews
return [

    'author' => [
        'enabled' => env('MODEL_REVIEWS_AUTHOR_ENABLED', true),

        /*
         * You can specify an auth driver here that gets user models.
         * If this is null we'll use the current Laravel auth driver.
        */
        'default_auth_driver' => null,

        /**
         * You can specify a custom resolver here that gets user models.
         */
        'resolver' => \RicardoLobo\LaravelModelReviews\ReviewAuthorResolver::class,
    ],

    'questions' => [
        'model' => \RicardoLobo\LaravelModelReviews\Models\Question::class,
        'table' => 'questions',
    ],

    'reviews' => [
        'model' => \RicardoLobo\LaravelModelReviews\Models\Review::class,
        'table' => 'reviews',
    ],

    'answers' => [
        'model' => \RicardoLobo\LaravelModelReviews\Models\Answer::class,
        'table' => 'review_question',
    ],

    'factories' => [
        'review' => \RicardoLobo\LaravelModelReviews\ReviewFactory::class,
    ],
];
