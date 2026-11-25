<?php

namespace Tests\Feature;

use App\Models\Idea;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SignalTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_is_idempotent_and_board_is_ready(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('ideas', 6);
        $this->get('/')->assertOk()->assertSee('A little more dark mode, please');
        $this->get('/ready')->assertOk()->assertJson(['status' => 'ready']);
    }

    public function test_submitting_an_idea_validates_and_persists_only_allowed_fields(): void
    {
        $this->post('/ideas', ['title' => 'Add project templates', 'description' => 'A useful collection of ready to launch apps.', 'category' => 'Experience', 'status' => 'Shipped', 'votes' => 999])
            ->assertRedirect(route('ideas.index', ['sort' => 'newest']));
        $this->assertDatabaseHas('ideas', ['title' => 'Add project templates', 'status' => 'Exploring', 'votes' => 0]);
        $this->post('/ideas', ['title' => 'a', 'description' => 'b', 'category' => 'invalid'])->assertSessionHasErrors(['title', 'description', 'category']);
    }

    public function test_one_browser_can_only_vote_once_and_a_second_browser_can_vote(): void
    {
        $idea = Idea::factory()->create();
        $this->withSession(['voter' => 'browser-one'])->post(route('ideas.vote', $idea))->assertRedirect();
        $this->withSession(['voter' => 'browser-one'])->post(route('ideas.vote', $idea))->assertRedirect();
        $this->assertSame(1, $idea->fresh()->votes);
        $this->withSession(['voter' => 'browser-two'])->post(route('ideas.vote', $idea))->assertRedirect();
        $this->assertSame(2, $idea->fresh()->votes);
        $this->assertDatabaseCount('idea_votes', 2);
    }

    public function test_board_filters_search_and_escapes_user_content(): void
    {
        Idea::factory()->create(['title' => '<script>alert(1)</script>', 'category' => 'Integrations']);
        Idea::factory()->create(['title' => 'Make previews faster', 'category' => 'Performance']);
        $this->get('/?category=Performance&q=previews')->assertSee('Make previews faster')->assertDontSee('alert(1)');
        $this->get('/')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/?status=invalid')->assertSessionHasErrors('status');
    }

    public function test_unknown_idea_returns_404_and_readiness_detects_missing_schema(): void
    {
        $this->post('/ideas/999/vote')->assertNotFound();
        DB::statement('DROP TABLE idea_votes');
        DB::statement('DROP TABLE ideas');
        $this->get('/ready')->assertStatus(503)->assertJson(['status' => 'unavailable']);
    }
}
