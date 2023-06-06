<?php

use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;
use RicardoLobo\LaravelModelReviews\Models\Question;
use RicardoLobo\LaravelModelReviews\Models\Review;
use RicardoLobo\LaravelModelReviews\Tests\Support\TestModel;
use RicardoLobo\LaravelModelReviews\Tests\Support\UserModel;

it('can create a review', function () {
    $question = Question::factory()->active()->create();

    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => $question->id,
            'rating' => RatingEnum::from(1),
        ],
    ]);

    expect($testModel->reviews()->count())
        ->toBe(1)
        ->and($testModel->reviews()->first()->answers()->count())->toBe(1);
});

it('can create a review with a comment', function () {
    $question = Question::factory()->active()->create();

    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => $question->id,
            'rating' => RatingEnum::from(1),
        ],
    ], 'test comment');

    expect($testModel->reviews()->count())
        ->toBe(1)
        ->and($testModel->reviews()->first()->answers()->count())->toBe(1)
        ->and($testModel->reviews()->first()->comment)->toBe('test comment');
});

it('can get the original question from a review answer', function () {
    $this->withoutExceptionHandling();

    $question = Question::factory()->active()->create();

    $originalTitle = $question->title;

    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => $question->id,
            'rating' => RatingEnum::from(1),
        ],
    ], 'test comment');

    $question->update(['title' => 'updated title']);

    $review = $testModel->reviews()->first();

    expect($review->answers->first()->question_title)->toBe($originalTitle);
});

it('can create a review with an author when authenticated', function () {
    $user = new UserModel();
    $user->forceFill(['id' => 1]);

    $this->actingAs($user);

    $question = Question::factory()->active()->create();

    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => $question->id,
            'rating' => RatingEnum::from(1),
        ],
    ]);

    /** @var Review $review */
    $review = $testModel->reviews()->first();

    expect($review->author_id)->toBe(1)
        ->and($review->author_type)->toBe(UserModel::class);
});

it('checks if the questions exist before creating a review', function () {
    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => 1,
            'rating' => RatingEnum::from(1),
        ],
    ]);
})->throws(Illuminate\Validation\ValidationException::class, 'The provided questions are invalid.');

it('checks if there are duplicated questions when creating a review', function () {
    /** @var TestModel $testModel */
    $testModel = TestModel::query()->create();

    $testModel->createReview([
        [
            'question_id' => 1,
            'rating' => RatingEnum::from(1),
        ],
        [
            'question_id' => 1,
            'rating' => RatingEnum::from(1),
        ],
    ]);
})->throws(Illuminate\Validation\ValidationException::class, 'The provided questions are duplicated.');

it('can create a review from a request', function () {
    \Illuminate\Support\Facades\Route::post('{id}/review', function ($id) {
        $testModel = TestModel::query()->findOrFail($id);

        $testModel->createReviewFromRequest();

        return response('success!');
    });

    $user = new UserModel();
    $user->forceFill(['id' => 1]);

    $this->actingAs($user);

    $question = Question::factory()->active()->create();

    $testModel = TestModel::query()->create();

    $this
        ->post("{$testModel->id}/review", [
            'review' => [
                [
                    'question_id' => $question->id,
                    'rating' => 1,
                ],
            ],
        ])
        ->assertOk();

    expect($testModel->reviews()->count())
        ->toBe(1)
        ->and($testModel->reviews()->first()->answers()->count())->toBe(1);
});

it('checks if the questions exist before creating a review from a request', function () {
    $this->withoutExceptionHandling();

    \Illuminate\Support\Facades\Route::post('{id}/review', function ($id) {
        $testModel = TestModel::query()->findOrFail($id);

        $testModel->createReviewFromRequest();

        return response('success!');
    });

    $user = new UserModel();
    $user->forceFill(['id' => 1]);

    $this->actingAs($user);

    $testModel = TestModel::query()->create();

    $this
        ->post("{$testModel->id}/review", [
            'review' => [
                [
                    'question_id' => 1,
                    'rating' => 1,
                ],
            ],
        ]);
})->throws(\Illuminate\Validation\ValidationException::class, 'The selected review.0.question_id is invalid.');

it('checks if the questions are duplicated before creating a review from a request', function () {
    $this->withoutExceptionHandling();

    \Illuminate\Support\Facades\Route::post('{id}/review', function ($id) {
        $testModel = TestModel::query()->findOrFail($id);

        $testModel->createReviewFromRequest();

        return response('success!');
    });

    $user = new UserModel();
    $user->forceFill(['id' => 1]);

    $this->actingAs($user);

    $question = Question::factory()->active()->create();

    $testModel = TestModel::query()->create();

    $this
        ->post("{$testModel->id}/review", [
            'review' => [
                [
                    'question_id' => $question->id,
                    'rating' => 1,
                ],
                [
                    'question_id' => $question->id,
                    'rating' => 1,
                ],
            ],
        ]);
})->throws(\Illuminate\Validation\ValidationException::class, 'The review.0.question_id field has a duplicate value.');
