<?php

namespace RicardoLobo\LaravelModelReviews\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use RicardoLobo\LaravelModelReviews\Enums\RatingEnum;

class CreateReviewFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'review' => ['array', 'required'],
            'review.*.question_id' => ['distinct', 'required', Rule::exists(config('model-reviews.questions.table'), 'id')],
            'review.*.rating' => ['required', new Enum(RatingEnum::class)],
        ];
    }
}
