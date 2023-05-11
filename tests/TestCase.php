<?php

namespace RicardoLobo\LaravelModelReviews\Tests;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use RicardoLobo\LaravelModelReviews\LaravelModelReviewsServiceProvider;
use RicardoLobo\LaravelModelReviews\Tests\Support\TestModel;

class TestCase extends Orchestra
{
    protected TestModel $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase($this->app);

        Factory::guessFactoryNamesUsing(
            fn(string $modelName
            ) => 'RicardoLobo\\LaravelModelReviews\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelModelReviewsServiceProvider::class,
            \Spatie\LaravelRay\RayServiceProvider::class,
        ];
    }

    public function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'sqlite');
            $config->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });

        $modelReviews = include __DIR__.'/../database/migrations/create_model_reviews_table.php.stub';
        $modelReviews->up();

        $questions = include __DIR__.'/../database/migrations/create_questions_table.php.stub';
        $questions->up();

        $answers = include __DIR__.'/../database/migrations/create_answers_table.php.stub';
        $answers->up();
    }

    protected function setupDatabase(?Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
        });
    }
}
