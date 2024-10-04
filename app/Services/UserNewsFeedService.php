<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class UserNewsFeedService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getNewsFeed(Request $request): LengthAwarePaginator
    {
        $user = $request->user();
        $preferredSources = $user->sources->pluck('id');
        $preferredAuthors = $user->authors->pluck('id');

        return Article::query()
            ->where(function ($query) use ($preferredSources, $preferredAuthors) {
                if ($preferredSources->isNotEmpty()) {
                    $query->whereHas('source', function ($query) use ($preferredSources) {
                        $query->whereIn('id', $preferredSources);
                    });
                }

                if ($preferredAuthors->isNotEmpty()) {
                    $query->orWhereHas('authors', function ($query) use ($preferredAuthors) {
                        $query->whereIn('id', $preferredAuthors);
                    });
                }
            })
            ->with(['source', 'authors'])
            ->paginate(10);
    }
}
