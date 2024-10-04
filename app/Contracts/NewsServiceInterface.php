<?php
namespace App\Contracts;

interface NewsServiceInterface
{
    public function fetchArticles(): array;
}

