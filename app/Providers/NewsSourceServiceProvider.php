<?php

namespace App\Providers;

use App\Services\ArticleService;
use App\Services\FetchArticleService;
use App\Services\NewsSources\NewsApiService;
use App\Services\NewsSources\NYTimesApiService;
use App\Services\NewsSources\TheGuardianApiService;
use Illuminate\Support\ServiceProvider;

class NewsSourceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NewsApiService::class, function () {
            return new NewsApiService(config('news_services.news_api_key'));
        });

        $this->app->singleton(NYTimesApiService::class, function () {
            return new NYTimesApiService(config('news_services.nytimes_api_key'));
        });

        $this->app->singleton(TheGuardianApiService::class, function () {
            return new TheGuardianApiService(config('news_services.the_guardian_api_key'));
        });

        $this->app->singleton(FetchArticleService::class, function ($app) {
            $newsServices = [
                $app->make(NewsAPIService::class),
                $app->make(NYTimesApiService::class),
                $app->make(TheGuardianApiService::class),
            ];
            return new FetchArticleService($newsServices, $app->make(ArticleService::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
