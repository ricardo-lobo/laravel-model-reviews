<?php

namespace RicardoLobo\LaravelModelReviews;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RicardoLobo\LaravelModelReviews\Concerns\Reviewable;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewAuthorResolverInterface;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewFactoryInterface;
use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;
use RicardoLobo\LaravelModelReviews\Events\ReviewCreatedEvent;
use RicardoLobo\LaravelModelReviews\Models\Question;

class ReviewFactory implements ReviewFactoryInterface
{
    public function __construct(
        protected Repository $config,
        protected ReviewAuthorResolverInterface $authorResolver
    ) {
    }

    public function create(Reviewable $reviewable, array $answers, string $comment = null): void
    {
        $answers = is_array(Arr::first($answers)) ? $answers : [$answers];

        $this->checkForDuplicatedQuestions($answers);

        $answers = $this->checkIfQuestionsExistAndAreEnabled($answers);

        $review = $reviewable->reviews()->make(['comment' => $comment]);

        if ($author = $this->getAuthor($reviewable)) {
            $review->author()->associate($author);
        }

        DB::transaction(function () use ($review, $answers) {
            $review->save();

            $review->questions()->sync(
                collect($answers)->mapWithKeys(fn (array $answer) => $this->createAnswerMappingFromArray($answer))
            );

            event(new ReviewCreatedEvent($review));
        });
    }

    protected function checkForDuplicatedQuestions(array $data): void
    {
        $ids = Arr::pluck($data, 'question_id');

        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages([
                'questions' => ['The provided questions are duplicated.'],
            ]);
        }
    }

    /**
     * Check if the questions exist and are enabled.
     * Merge the questions properties with the data array.
     */
    protected function checkIfQuestionsExistAndAreEnabled(array $data): array
    {
        $ids = Arr::pluck($data, 'question_id');

        $model = $this->config->get('model-reviews.questions.model');

        /** @var Question|Model $instance */
        $instance = new $model;

        try {
            $questions = $instance->query()->where('active', true)->findOrFail($ids);

            return $questions
                ->map(function (Question $question) use ($data) {
                    $answer = Arr::first($data, fn (array $answer) => $answer['question_id'] === $question->getKey());

                    return array_merge($answer, [
                        'question' => $question,
                    ]);
                })
                ->toArray();
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'questions' => ['The provided questions are invalid.'],
            ]);
        }
    }

    protected function createAnswerMappingFromArray(array $data): array
    {
        $rating = $data['rating'] instanceof RatingEnum
            ? $data['rating']
            : RatingEnum::from($data['rating']);

        return [
            $data['question_id'] => [
                'rating' => $rating,
                'question_title' => $data['question']->title,
            ],
        ];
    }

    protected function getAuthor(Reviewable $reviewable): ?Model
    {
        if (! $this->config->get('model-reviews.author.enabled')) {
            return null;
        }

        return $reviewable->getReviewAuthor() ?: $this->authorResolver->resolve();
    }
}
