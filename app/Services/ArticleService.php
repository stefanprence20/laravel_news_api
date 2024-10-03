<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleService
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function paginate(Request $request): mixed
    {
        $perPage = $request->input('per_page', 10);

        return Article::paginate($perPage);
    }

    public function search(Request $request): mixed
    {
        $perPage = $request->input('per_page', 10);

        return Article::paginate($perPage);
    }
}
