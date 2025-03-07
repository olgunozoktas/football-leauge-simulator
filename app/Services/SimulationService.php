<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimulationService
{
    /**
     * The team service instance.
     *
     * @var TeamService
     */
    protected $teamService;

    /**
     * The fixture service instance.
     *
     * @var FixtureService
     */
    protected $fixtureService;

    /**
     * The match service instance.
     *
     * @var MatchService
     */
    protected $matchService;

    /**
     * The prediction service instance.
     *
     * @var PredictionService
     */
    protected $predictionService;

    /**
     * The simulation state service instance.
     *
     * @var SimulationStateService
     */
    protected $stateService;

    /**
     * Create a new service instance.
     *
     * @param TeamService $teamService
     * @param FixtureService $fixtureService
     * @param MatchService $matchService
     * @param PredictionService $predictionService
     * @param SimulationStateService $stateService
     * @return void
     */
    public function __construct(
        TeamService            $teamService,
        FixtureService         $fixtureService,
        MatchService           $matchService,
        PredictionService      $predictionService,
        SimulationStateService $stateService
    )
    {
        $this->teamService = $teamService;
        $this->fixtureService = $fixtureService;
        $this->matchService = $matchService;
        $this->predictionService = $predictionService;
        $this->stateService = $stateService;

        // Set the prediction service on the match service
        $this->matchService->setPredictionService($this->predictionService);
    }

    /**
     * Generate fixtures for the teams.
     *
     * @return void
     */
    public function generateFixtures(): void
    {
        $this->fixtureService->generateFixtures();
        $this->resetSimulation();
    }

    /**
     * Initialize teams with default values.
     *
     * @return void
     */
    public function initializeTeams(): void
    {
        $this->teamService->initializeTeams();
    }

    /**
     * Calculate match result based on team power.
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @return array
     */
    public function calculateMatchResult(Team $homeTeam, Team $awayTeam): array
    {
        return $this->matchService->calculateMatchResult($homeTeam, $awayTeam);
    }

    /**
     * Update team stats based on match result.
     *
     * @param string $homeTeamId
     * @param string $awayTeamId
     * @param int $homeGoals
     * @param int $awayGoals
     * @param bool $isReset
     * @return void
     */
    public function updateTeamStats($homeTeamId, $awayTeamId, $homeGoals, $awayGoals, $isReset = false)
    {
        $this->teamService->updateTeamStats($homeTeamId, $awayTeamId, $homeGoals, $awayGoals, $isReset);
    }

    /**
     * Simulate a single match.
     *
     * @param string $matchId
     * @return \App\Models\Fixture|null
     */
    public function simulateMatch($matchId)
    {
        return $this->matchService->simulateMatch($matchId);
    }

    /**
     * Simulate all matches for the next week.
     *
     * @return array
     */
    public function simulateNextWeek(): array
    {
        $state = $this->stateService->getCurrentState();

        $maxWeek = Fixture::max('week');

        if ($state->current_week >= $maxWeek) {
            return ['message' => 'Simulation already complete'];
        }

        $nextWeek = $state->current_week + 1;
        $weekFixtures = $this->fixtureService->getFixturesByWeek($nextWeek);
        $simulatedMatches = [];

        foreach ($weekFixtures as $match) {
            if (!$match->played) {
                $simulatedMatch = $this->simulateMatch($match->id);
                if ($simulatedMatch) {
                    $simulatedMatches[] = $simulatedMatch;
                }
            }
        }

        $this->stateService->updateCurrentWeek($nextWeek);

        if ($nextWeek >= 4) {
            $this->predictionService->updatePredictions();
        }

        return [
            'week' => $nextWeek,
            'matches' => $simulatedMatches,
            'is_complete' => $nextWeek === $maxWeek,
        ];
    }

    /**
     * Simulate all remaining weeks.
     *
     * @return array
     */
    public function simulateAllWeeks(): array
    {
        $state = $this->stateService->getCurrentState();
        $results = [];

        $maxWeek = Fixture::max('week');

        for ($week = $state->current_week + 1; $week <= $maxWeek; $week++) {
            $weekFixtures = $this->fixtureService->getFixturesByWeek($week);
            $weekResults = [];

            foreach ($weekFixtures as $match) {
                if (!$match->played) {
                    $simulatedMatch = $this->simulateMatch($match->id);
                    if ($simulatedMatch) {
                        $weekResults[] = $simulatedMatch;
                    }
                }
            }

            $results[$week] = $weekResults;
        }

        $this->stateService->completeSimulation();

        $this->predictionService->updatePredictions();

        return [
            'results' => $results,
            'is_complete' => true,
        ];
    }

    /**
     * Reset the simulation.
     *
     * @return void
     */
    public function resetSimulation(): void
    {
        // Reset match results
        $playedMatches = Fixture::query()->where('played', true)->get();

        DB::beginTransaction();
        try {
            foreach ($playedMatches as $match) {
                $this->teamService->applyStats($match, true);
            }

            $this->fixtureService->resetFixtures();
            $this->teamService->resetTeamStats();
            $this->stateService->resetState();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("An error occurred while resetting simulation", [
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Update match result manually.
     *
     * @param string $matchId
     * @param int $homeGoals
     * @param int $awayGoals
     * @return \App\Models\Fixture|null
     */
    public function updateMatchResult($matchId, $homeGoals, $awayGoals)
    {
        return $this->matchService->updateMatchResult($matchId, $homeGoals, $awayGoals);
    }

    /**
     * Calculate championship predictions.
     *
     * @return void
     */
    public function updatePredictions()
    {
        $this->predictionService->updatePredictions();
    }
}
