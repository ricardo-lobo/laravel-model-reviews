<?php

namespace RicardoLobo\LaravelModelReviews;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use RicardoLobo\LaravelModelReviews\Concerns\ReviewAuthorResolverInterface;

class ReviewAuthorResolver implements ReviewAuthorResolverInterface
{
    protected AuthManager $authManager;

    protected ?string $authDriver;

    public function __construct(Repository $config, AuthManager $authManager)
    {
        $this->authManager = $authManager;

        $this->authDriver = $config->get('model-reviews.default_auth_driver');
    }

    public function resolve(Model|int|string|null $subject = null): ?Model
    {
        return $this->getAuthor($subject);
    }

    protected function resolveUsingId(int|string $subject): ?Model
    {
        $guard = $this->authManager->guard($this->authDriver);

        $provider = method_exists($guard, 'getProvider') ? $guard->getProvider() : null;
        $model = method_exists($provider, 'retrieveById') ? $provider->retrieveById($subject) : null;

        return $model instanceof Model ? $model : null;
    }

    protected function getAuthor(Model|int|string|null $subject = null): ?Model
    {
        if ($subject instanceof Model) {
            return $subject;
        }

        if (is_null($subject)) {
            return $this->getDefaultAuthor();
        }

        return $this->resolveUsingId($subject);
    }

    protected function isResolvable(mixed $model): bool
    {
        return $model instanceof Model || is_null($model);
    }

    protected function getDefaultAuthor(): Model|Authenticatable|null
    {
        return $this->authManager->guard($this->authDriver)->user();
    }
}
