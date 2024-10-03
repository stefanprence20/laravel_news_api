<?php
namespace App\Contracts;

interface NewsServiceInterface
{
    public function fetchDailyArticles(): array;
}

