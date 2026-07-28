<?php

use RicardoLobo\LaravelModelReviews\Models\Answer;
use RicardoLobo\LaravelModelReviews\Models\Question;
use RicardoLobo\LaravelModelReviews\Models\Review;
use RicardoLobo\LaravelModelReviews\ReviewAuthorResolver;
use RicardoLobo\LaravelModelReviews\ReviewFactory;

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
        'resolver' => ReviewAuthorResolver::class,
    ],

    'questions' => [
        'model' => Question::class,
        'table' => 'questions',
    ],

    'reviews' => [
        'model' => Review::class,
        'table' => 'reviews',
    ],

    'answers' => [
        'model' => Answer::class,
        'table' => 'review_question',
    ],

    'factories' => [
        'review' => ReviewFactory::class,
    ],
];
