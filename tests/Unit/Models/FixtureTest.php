<?php

namespace Tests\Unit\Models;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_fixture(): void
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

        $fixture = Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => false,
        ]);

        $this->assertInstanceOf(Fixture::class, $fixture);
        $this->assertEquals(1, $fixture->week);
        $this->assertEquals($homeTeam->id, $fixture->home_team_id);
        $this->assertEquals($awayTeam->id, $fixture->away_team_id);
        $this->assertFalse($fixture->played);
        $this->assertNull($fixture->home_goals);
        $this->assertNull($fixture->away_goals);
    }

    public function test_fixture_relationships(): void
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

        $fixture = Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => false,
        ]);

        $this->assertInstanceOf(Team::class, $fixture->homeTeam);
        $this->assertInstanceOf(Team::class, $fixture->awayTeam);
        $this->assertEquals($homeTeam->id, $fixture->homeTeam->id);
        $this->assertEquals($awayTeam->id, $fixture->awayTeam->id);
    }

    public function test_fixture_update(): void
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

        $fixture = Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => false,
        ]);

        $fixture->played = true;
        $fixture->home_goals = 2;
        $fixture->away_goals = 1;
        $fixture->save();

        $fixture = $fixture->fresh();

        $this->assertTrue($fixture->played);
        $this->assertEquals(2, $fixture->home_goals);
        $this->assertEquals(1, $fixture->away_goals);
    }
}
