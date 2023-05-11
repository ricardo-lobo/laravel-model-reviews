<?php

namespace RicardoLobo\LaravelModelReviews;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use RicardoLobo\LaravelModelReviews\Concerns\Reviewable;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewAuthorResolverInterface;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewFactoryInterface;
use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;
use RicardoLobo\LaravelModelReviews\Models\Review;

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

        $this->checkIfQuestionsExistAndAreEnabled($answers);

        /** @var Review $review */
        $review = $reviewable->reviews()->make(['comment' => $comment]);

        if ($author = $this->getAuthor()) {
            $review->author()->associate($author);
        }

        $review->save();

        $review->questions()->sync(
            collect($answers)->mapWithKeys(fn(array $answer) => $this->createAnswerMappingFromArray($answer))
        );
    }

    protected function checkForDuplicatedQuestions(array $data): void
    {
        $ids = Arr::pluck($data, 'question_id');

        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages([
                'questions' => ['The provided questions are duplicated.']
            ]);
        }
    }

    protected function checkIfQuestionsExistAndAreEnabled(array $data): void
    {
        $ids = Arr::pluck($data, 'question_id');

        $model = $this->config->get('model-reviews.questions.model');

        /** @var Model $instance */
        $instance = new $model;

        try {
            $instance->query()->where('active', true)->findOrFail($ids);
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
            ]
        ];
    }

    protected function getAuthor(): ?Model
    {
        if (! $this->config->get('model-reviews.author.enabled')) {
            return null;
        }

        return $this->authorResolver->resolve();
    }
}
