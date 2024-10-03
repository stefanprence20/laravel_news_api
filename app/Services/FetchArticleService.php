<?php

namespace App\Services;

class FetchArticleService
{
    protected array $newsServices;
    public ArticleService $articleService;

    public function __construct(array $newsServices, ArticleService $articleService)
    {
        $this->newsServices = $newsServices;
        $this->articleService = $articleService;
    }

    /**
     * @return void
     */
    public function fetchAndStoreArticles(): void
    {
        foreach ($this->newsServices as $newsService) {
            $articles = $newsService->fetchDailyArticles();

            foreach ($articles as $article) {
                $this->articleService->save($article);
            }
        }
    }
}
