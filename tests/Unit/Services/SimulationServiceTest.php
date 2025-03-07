<?php

namespace Tests\Unit\Services;

use App\Models\Fixture;
use App\Models\SimulationState;
use App\Models\Team;
use App\Services\SimulationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SimulationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The simulation service instance.
     *
     * @var SimulationService
     */
    protected $simulationService;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->simulationService = app(SimulationService::class);
    }

    /**
     * Test initialize teams method.
     */
    public function test_initialize_teams(): void
    {
        $this->simulationService->initializeTeams();

        $teams = Team::all();

        $this->assertCount(4, $teams);
        $this->assertEquals('Manchester City', $teams[0]->name);
        $this->assertEquals('Liverpool', $teams[1]->name);
        $this->assertEquals('Arsenal', $teams[2]->name);
        $this->assertEquals('Chelsea', $teams[3]->name);
    }

    /**
     * Test generate fixtures method.
     */
    public function test_generate_fixtures(): void
    {
        $this->simulationService->initializeTeams();
        $this->simulationService->generateFixtures();

        $fixtures = Fixture::all();
        $teams = Team::all();

        $this->assertCount(12, $fixtures);

        $weekCounts = [];
        $maxWeek = Fixture::max('week');
        for ($i = 1; $i <= $maxWeek; $i++) {
            $count = Fixture::query()->where('week', $i)->count();
            $weekCounts[$i] = $count;
            $this->assertGreaterThan(0, $count, "Week $i should have fixtures");
        }

        $this->assertEquals(12, array_sum($weekCounts), "Total fixtures across all weeks should be 12");

        foreach ($teams as $team) {
            $this->assertCount(3, Fixture::query()->where('home_team_id', $team->id)->get());
            $this->assertCount(3, Fixture::query()->where('away_team_id', $team->id)->get());
        }
    }

    /**
     * Test calculate match result method.
     */
    public function test_calculate_match_result(): void
    {
        $homeTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Home Team',
            'power' => 85,
        ]);

        $awayTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Away Team',
            'power' => 75,
        ]);

        $result = $this->simulationService->calculateMatchResult($homeTeam, $awayTeam);

        $this->assertArrayHasKey('home_goals', $result);
        $this->assertArrayHasKey('away_goals', $result);
        $this->assertIsNumeric($result['home_goals']);
        $this->assertIsNumeric($result['away_goals']);
        $this->assertGreaterThanOrEqual(0, $result['home_goals']);
        $this->assertGreaterThanOrEqual(0, $result['away_goals']);
    }

    /**
     * Test update team stats method.
     */
    public function test_update_team_stats(): void
    {
        $homeTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Home Team',
            'power' => 85,
        ]);

        $awayTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Away Team',
            'power' => 75,
        ]);

        $this->simulationService->updateTeamStats($homeTeam->id, $awayTeam->id, 2, 1);

        $homeTeam = $homeTeam->fresh();
        $awayTeam = $awayTeam->fresh();

        $this->assertEquals(1, $homeTeam->played);
        $this->assertEquals(1, $homeTeam->won);
        $this->assertEquals(0, $homeTeam->drawn);
        $this->assertEquals(0, $homeTeam->lost);
        $this->assertEquals(2, $homeTeam->goals_for);
        $this->assertEquals(1, $homeTeam->goals_against);
        $this->assertEquals(1, $homeTeam->goal_difference);
        $this->assertEquals(3, $homeTeam->points);

        $this->assertEquals(1, $awayTeam->played);
        $this->assertEquals(0, $awayTeam->won);
        $this->assertEquals(0, $awayTeam->drawn);
        $this->assertEquals(1, $awayTeam->lost);
        $this->assertEquals(1, $awayTeam->goals_for);
        $this->assertEquals(2, $awayTeam->goals_against);
        $this->assertEquals(-1, $awayTeam->goal_difference);
        $this->assertEquals(0, $awayTeam->points);

        $this->simulationService->updateTeamStats($homeTeam->id, $awayTeam->id, 2, 1, true);

        $homeTeam = $homeTeam->fresh();
        $awayTeam = $awayTeam->fresh();

        $this->assertEquals(0, $homeTeam->played);
        $this->assertEquals(0, $homeTeam->won);
        $this->assertEquals(0, $homeTeam->points);
        $this->assertEquals(0, $awayTeam->played);
        $this->assertEquals(0, $awayTeam->lost);
        $this->assertEquals(0, $awayTeam->points);
    }

    /**
     * Test simulate match method.
     */
    public function test_simulate_match(): void
    {
        $homeTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Home Team',
            'power' => 85,
        ]);

        $awayTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Away Team',
            'power' => 75,
        ]);

        $fixture = Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => false,
        ]);

        SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $match = $this->simulationService->simulateMatch($fixture->id);

        $this->assertInstanceOf(Fixture::class, $match);
        $this->assertTrue($match->played);
        $this->assertNotNull($match->home_goals);
        $this->assertNotNull($match->away_goals);

        $homeTeam = $homeTeam->fresh();
        $awayTeam = $awayTeam->fresh();

        $this->assertEquals(1, $homeTeam->played);
        $this->assertEquals(1, $awayTeam->played);
    }

    /**
     * Test simulate next week method.
     */
    public function test_simulate_next_week(): void
    {
        $this->simulationService->initializeTeams();
        $this->simulationService->generateFixtures();

        SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $result = $this->simulationService->simulateNextWeek();

        $this->assertEquals(1, $result['week']);
        $this->assertCount(6, $result['matches']);
        $this->assertFalse($result['is_complete']);

        $state = SimulationState::getCurrentState();
        $this->assertEquals(1, $state->current_week);
        $this->assertFalse($state->is_simulation_complete);

        $this->assertEquals(6, Fixture::query()->where('week', 1)->where('played', true)->count());
    }

    /**
     * Test simulate all weeks method.
     */
    public function test_simulate_all_weeks(): void
    {
        $this->simulationService->initializeTeams();
        $this->simulationService->generateFixtures();

        SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $result = $this->simulationService->simulateAllWeeks();

        $this->assertTrue($result['is_complete']);
        $this->assertArrayHasKey('results', $result);

        $maxWeek = Fixture::max('week');
        $this->assertCount($maxWeek, $result['results']);

        $state = SimulationState::getCurrentState();
        $this->assertEquals($maxWeek, $state->current_week);
        $this->assertTrue($state->is_simulation_complete);

        $this->assertEquals(12, Fixture::query()->where('played', true)->count());
    }

    /**
     * Test reset simulation method.
     */
    public function test_reset_simulation(): void
    {
        $this->simulationService->initializeTeams();
        $this->simulationService->generateFixtures();

        SimulationState::query()->create([
            'current_week' => 3,
            'is_simulation_complete' => false,
        ]);

        $fixtures = Fixture::query()->where('week', 1)->get();
        foreach ($fixtures as $fixture) {
            $this->simulationService->simulateMatch($fixture->id);
        }

        $this->simulationService->resetSimulation();

        $state = SimulationState::getCurrentState();
        $this->assertEquals(0, $state->current_week);
        $this->assertFalse($state->is_simulation_complete);

        $this->assertEquals(0, Fixture::query()->where('played', true)->count());

        $teams = Team::all();
        foreach ($teams as $team) {
            $this->assertEquals(0, $team->played);
            $this->assertEquals(0, $team->won);
            $this->assertEquals(0, $team->drawn);
            $this->assertEquals(0, $team->lost);
            $this->assertEquals(0, $team->goals_for);
            $this->assertEquals(0, $team->goals_against);
            $this->assertEquals(0, $team->goal_difference);
            $this->assertEquals(0, $team->points);
        }
    }

    /**
     * Test update match result method.
     */
    public function test_update_match_result(): void
    {
        $homeTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Home Team',
            'power' => 85,
        ]);

        $awayTeam = Team::query()->create([
            'id' => Str::uuid(),
            'name' => 'Away Team',
            'power' => 75,
        ]);

        $fixture = Fixture::query()->create([
            'id' => Str::uuid(),
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played' => true,
            'home_goals' => 2,
            'away_goals' => 1,
        ]);

        SimulationState::query()->create([
            'current_week' => 1,
            'is_simulation_complete' => false,
        ]);

        $this->simulationService->updateTeamStats($homeTeam->id, $awayTeam->id, 2, 1);

        $match = $this->simulationService->updateMatchResult($fixture->id, 1, 3);

        $this->assertInstanceOf(Fixture::class, $match);
        $this->assertEquals(1, $match->home_goals);
        $this->assertEquals(3, $match->away_goals);

        $homeTeam = $homeTeam->fresh();
        $awayTeam = $awayTeam->fresh();

        $this->assertEquals(1, $homeTeam->played);
        $this->assertEquals(0, $homeTeam->won);
        $this->assertEquals(0, $homeTeam->drawn);
        $this->assertEquals(1, $homeTeam->lost);
        $this->assertEquals(1, $homeTeam->goals_for);
        $this->assertEquals(3, $homeTeam->goals_against);
        $this->assertEquals(-2, $homeTeam->goal_difference);
        $this->assertEquals(0, $homeTeam->points);

        $this->assertEquals(1, $awayTeam->played);
        $this->assertEquals(1, $awayTeam->won);
        $this->assertEquals(0, $awayTeam->drawn);
        $this->assertEquals(0, $awayTeam->lost);
        $this->assertEquals(3, $awayTeam->goals_for);
        $this->assertEquals(1, $awayTeam->goals_against);
        $this->assertEquals(2, $awayTeam->goal_difference);
        $this->assertEquals(3, $awayTeam->points);
    }

    /**
     * Test update predictions method.
     */
    public function test_update_predictions(): void
    {
        $this->simulationService->initializeTeams();
        $this->simulationService->generateFixtures();

        SimulationState::query()->create([
            'current_week' => 4,
            'is_simulation_complete' => false,
        ]);

        $fixtures = Fixture::query()->where('week', '<=', 4)->get();
        foreach ($fixtures as $fixture) {
            $this->simulationService->simulateMatch($fixture->id);
        }

        $this->simulationService->updatePredictions();

        $teams = Team::all();
        $totalProbability = 0;

        foreach ($teams as $team) {
            $totalProbability += $team->win_probability;
            $this->assertGreaterThanOrEqual(0, $team->win_probability);
            $this->assertLessThanOrEqual(100, $team->win_probability);
        }

        $this->assertEquals(100, $totalProbability);
    }
}
