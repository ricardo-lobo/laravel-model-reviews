<?php

namespace RicardoLobo\LaravelModelReviews\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RicardoLobo\LaravelModelReviews\LaravelModelReviews
 */
class LaravelModelReviews extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RicardoLobo\LaravelModelReviews\LaravelModelReviews::class;
    }
}
