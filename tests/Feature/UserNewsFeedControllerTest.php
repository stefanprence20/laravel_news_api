<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNewsFeedControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_personalized_user_news_feed()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Article::factory()->count(20)->create();

        $response = $this->getJson('api/v1/users/news-feed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'url', 'published_at', 'source']
                ],
                'meta' => ['current_page', 'last_page', 'per_page'],
            ]);
    }
}
