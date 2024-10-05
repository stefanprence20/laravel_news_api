<?php

namespace Tests\Feature;

use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function add_user_preferences()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $data = [
            'preference_type' => 'source',
            'preference_ids' => Source::factory()->count(3)->create()->pluck('id')->toArray(),
        ];

        $response = $this->postJson('api/v1/users/preferences', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'preferable_type', 'preferable_id']
                ],
            ]);
        $this->assertDatabaseHas('preferables', ['user_id' => $user->id, 'preferable_type' => Source::class]);
    }

    /** @test */
    public function get_user_preferences()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $preferences = Source::factory()->count(5)->create();
        $user->sources()->sync($preferences->pluck('id')->toArray());

        $response = $this->getJson('api/v1/users/preferences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'preferable_type', 'preferable_id']
                ],
            ]);

    }
}
