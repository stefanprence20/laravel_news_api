<?php

namespace App\Console\Commands;

use App\Services\FetchArticleService;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store daily news articles';

    protected FetchArticleService $fetchArticleService;

    public function __construct(FetchArticleService $fetchArticleService)
    {
        parent::__construct();
        $this->fetchArticleService = $fetchArticleService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->fetchArticleService->fetchAndStoreArticles();
        $this->info('Daily news fetched successfully.');
    }
}
