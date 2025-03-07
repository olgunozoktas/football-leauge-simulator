<?php

namespace Tests\Unit\Models;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_team(): void
    {
        $team = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Test Team',
            'power' => 85,
        ]);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Test Team', $team->name);
        $this->assertEquals(85, $team->power);
        $this->assertEquals(0, $team->played);
        $this->assertEquals(0, $team->won);
        $this->assertEquals(0, $team->drawn);
        $this->assertEquals(0, $team->lost);
        $this->assertEquals(0, $team->goals_for);
        $this->assertEquals(0, $team->goals_against);
        $this->assertEquals(0, $team->goal_difference);
        $this->assertEquals(0, $team->points);
        $this->assertEquals(0, $team->win_probability);
    }

    public function test_team_relationships(): void
    {
        $homeTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Home Team',
            'power' => 85,
        ]);

        $awayTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Away Team',
            'power' => 80,
        ]);

        Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => false,
        ]);

        Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 4,
            'home_team_id' => $awayTeam->id,
            'away_team_id' => $homeTeam->id,
            'played' => false,
        ]);

        $this->assertCount(1, $homeTeam->homeFixtures);
        $this->assertCount(1, $homeTeam->awayFixtures);
        $this->assertCount(1, $awayTeam->homeFixtures);
        $this->assertCount(1, $awayTeam->awayFixtures);

        $this->assertInstanceOf(Fixture::class, $homeTeam->homeFixtures->first());
        $this->assertInstanceOf(Fixture::class, $homeTeam->awayFixtures->first());
    }

    public function test_team_stats_update(): void
    {
        $team = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Test Team',
            'power' => 85,
        ]);

        $team->played = 1;
        $team->won = 1;
        $team->goals_for = 3;
        $team->goals_against = 1;
        $team->goal_difference = 2;
        $team->points = 3;
        $team->save();

        $team = $team->fresh();

        $this->assertEquals(1, $team->played);
        $this->assertEquals(1, $team->won);
        $this->assertEquals(0, $team->drawn);
        $this->assertEquals(0, $team->lost);
        $this->assertEquals(3, $team->goals_for);
        $this->assertEquals(1, $team->goals_against);
        $this->assertEquals(2, $team->goal_difference);
        $this->assertEquals(3, $team->points);
    }
}
