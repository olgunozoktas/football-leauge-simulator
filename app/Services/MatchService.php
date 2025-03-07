<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\SimulationState;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class MatchService
{
    /**
     * The team service instance.
     *
     * @var TeamService
     */
    protected $teamService;

    /**
     * The prediction service instance.
     *
     * @var PredictionService
     */
    protected $predictionService;

    /**
     * Create a new service instance.
     *
     * @param TeamService $teamService
     * @return void
     */
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Set the prediction service.
     *
     * @param PredictionService $predictionService
     * @return void
     */
    public function setPredictionService(PredictionService $predictionService): void
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Calculate match result based on team power.
     *
     * @param object $homeTeam
     * @param object $awayTeam
     * @return array
     */
    public function calculateMatchResult($homeTeam, $awayTeam): array
    {
        $homeExpectedGoals = $homeTeam->power / 20 + 0.5; // Home advantage
        $awayExpectedGoals = $awayTeam->power / 25;

        $homeGoals = max(0, round($homeExpectedGoals + (mt_rand(-100, 100) / 100)));
        $awayGoals = max(0, round($awayExpectedGoals + (mt_rand(-100, 100) / 100)));

        return ['home_goals' => $homeGoals, 'away_goals' => $awayGoals];
    }

    /**
     * Simulate a single match.
     *
     * @param int $matchId
     * @return Fixture|null
     */
    public function simulateMatch(int $matchId): ?Fixture
    {
        $match = Fixture::query()->with('homeTeam', 'awayTeam')->find($matchId);

        if (!$match || $match->played) {
            return null;
        }

        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;

        if (!$homeTeam || !$awayTeam) {
            return null;
        }

        $result = $this->calculateMatchResult($homeTeam, $awayTeam);

        DB::beginTransaction();
        try {
            $match->home_goals = $result['home_goals'];
            $match->away_goals = $result['away_goals'];
            $match->played = true;
            $match->save();

            $this->teamService->applyStats($match);

            $state = SimulationState::getCurrentState();
            if ($state->current_week >= 3 && $this->predictionService) {
                $this->predictionService->updatePredictions();
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("An error occurred while simulating match", [
                'exception' => $exception->getMessage()
            ]);
        }

        return $match;
    }

    /**
     * Update match result manually.
     *
     * @param string $matchId
     * @param int $homeGoals
     * @param int $awayGoals
     * @return Fixture|null
     */
    public function updateMatchResult(string $matchId, int $homeGoals, int $awayGoals): ?Fixture
    {
        $match = Fixture::query()->find($matchId);

        if (!$match || !$match->played) {
            Log::info("Match not played yet", [
                'matchId' => $matchId,
            ]);
            return null;
        }

        DB::beginTransaction();
        try {
            $this->teamService->applyStats($match, true);

            $match->home_goals = $homeGoals;
            $match->away_goals = $awayGoals;
            $match->save();

            $this->teamService->applyStats($match);

            $state = SimulationState::getCurrentState();
            if ($state->current_week >= 4 && $this->predictionService) {
                $this->predictionService->updatePredictions();
            }

            DB::commit();

            return $match;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("An error occurred while updating match results", [
                'exception' => $exception,
            ]);
        }

        return null;
    }
}
