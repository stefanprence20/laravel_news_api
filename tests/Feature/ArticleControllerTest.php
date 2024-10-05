<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_articles()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Article::factory()->count(20)->create();

        $response = $this->getJson('api/v1/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'published_at', 'source']
                ],
                'meta' => ['current_page', 'last_page', 'per_page'],
            ]);
    }

    /** @test */
    public function it_searches_the_articles()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $source = Source::factory()->create(['name' => 'The Guardian']);
        $author = Author::factory()->create(['name' => 'Lisa Adamber']);
        Article::factory()->create([
            'title' => 'The match has been ...',
            'source_id' => $source->id,
            'published_at' => now(),
        ])->authors()->attach($author);

        $response = $this->getJson('api/v1/articles/search', [
            'keyword' => 'match',
            'source' => 'The Guardian',
            'author' => 'Lisa Adamber',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'The match has been ...']);
    }

    /** @test */
    public function it_returns_specific_article()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $article = Article::factory()->create();

        $response = $this->getJson('api/v1/articles/'.$article->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $article->id]);
    }
}
