<?php

declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\BlockRepositoryInterface;
use App\Interfaces\CollectionRepositoryInterface;
use App\Interfaces\FAQRepositoryInterface;
use App\Interfaces\IconRepositoryInterface;
use App\Interfaces\IntegrationRepositoryInterface;
use App\Interfaces\PlanRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ReviewRepositoryInterface;
use App\Interfaces\ThemeRepositoryInterface;
use App\Interfaces\TutorialRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BlockRepository;
use App\Repositories\CollectionRepository;
use App\Repositories\FAQRepository;
use App\Repositories\IconRepository;
use App\Repositories\IntegrationRepository;
use App\Repositories\PlanRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ThemeRepository;
use App\Repositories\TutorialRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BlockRepositoryInterface::class, BlockRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CollectionRepositoryInterface::class, CollectionRepository::class);
        $this->app->bind(IconRepositoryInterface::class, IconRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(TutorialRepositoryInterface::class, TutorialRepository::class);
        $this->app->bind(FAQRepositoryInterface::class, FAQRepository::class);
        $this->app->bind(IntegrationRepositoryInterface::class, IntegrationRepository::class);
        $this->app->bind(ThemeRepositoryInterface::class, ThemeRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
