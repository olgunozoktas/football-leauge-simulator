<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\SimulationState;
use App\Models\Team;

class PredictionService
{
    /**
     * The match service instance.
     *
     * @var MatchService
     */
    protected $matchService;

    /**
     * Create a new service instance.
     *
     * @param MatchService $matchService
     * @return void
     */
    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    /**
     * Calculate championship predictions using Monte Carlo simulation.
     * Ref for Monte Carlo Simulation: https://aws.amazon.com/tr/what-is/monte-carlo-simulation/
     *
     * @return void
     */
    public function updatePredictions(): void
    {
        $teams = Team::all();
        $state = SimulationState::getCurrentState();

        $sortedTeams = $teams->sortBy([
            ['points', 'desc'],
            ['goal_difference', 'desc'],
            ['goals_for', 'desc'],
        ])->values();

        $remainingMatches = Fixture::query()->where('played', false)->get();
        $remainingWeeks = 6 - $state->current_week;

        if ($remainingMatches->count() === 0) {
            foreach ($teams as $team) {
                $team->win_probability = ($team->id === $sortedTeams[0]->id) ? 100 : 0;
                $team->save();
            }
            return;
        }

        // If we're in the last week and there's a clear leader with an insurmountable lead
        if ($remainingWeeks <= 1) {
            $leader = $sortedTeams[0];
            $secondPlace = $sortedTeams[1];

            $pointsDifference = $leader->points - $secondPlace->points;
            $remainingPointsPossible = $remainingMatches->count() * 3;

            if ($pointsDifference > $remainingPointsPossible) {
                foreach ($teams as $team) {
                    $team->win_probability = ($team->id === $leader->id) ? 100 : 0;
                    $team->save();
                }
                return;
            }
        }

        $numSimulations = 1000;

        $winCounts = collect($teams)->pluck('id')->mapWithKeys(fn($id) => [$id => 0])->toArray();

        collect(range(1, $numSimulations))->each(function () use ($teams, $remainingMatches, &$winCounts) {
            $simulatedTeams = $teams->mapWithKeys(function ($team) {
                return [$team->id => (object)[
                    'id' => $team->id,
                    'points' => $team->points,
                    'goal_difference' => $team->goal_difference,
                    'goals_for' => $team->goals_for,
                    'power' => $team->power
                ]];
            })->toArray();

            // Simulate all remaining matches
            $remainingMatches->each(function ($match) use (&$simulatedTeams) {
                $homeTeam = (object)[
                    'id' => $match->home_team_id,
                    'power' => $simulatedTeams[$match->home_team_id]->power
                ];

                $awayTeam = (object)[
                    'id' => $match->away_team_id,
                    'power' => $simulatedTeams[$match->away_team_id]->power
                ];

                // Simulate match result
                $result = $this->matchService->calculateMatchResult($homeTeam, $awayTeam);

                // Update simulated team stats
                $this->updateSimulatedTeamStats(
                    $simulatedTeams,
                    $match->home_team_id,
                    $match->away_team_id,
                    $result['home_goals'],
                    $result['away_goals']
                );
            });

            // Determine the winner of this simulation
            $winner = $this->determineSimulationWinner($simulatedTeams);
            $winCounts[$winner]++;
        });

        // Update win probabilities for each team
        $teams->each(function ($team) use ($numSimulations, $winCounts) {
            $team->win_probability = round(($winCounts[$team->id] / $numSimulations) * 100);
            $team->save();
        });
    }

    /**
     * Update simulated team stats after a match.
     *
     * @param array $simulatedTeams
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @param int $homeGoals
     * @param int $awayGoals
     * @return void
     */
    private function updateSimulatedTeamStats(array &$simulatedTeams, int $homeTeamId, int $awayTeamId, int $homeGoals, int $awayGoals): void
    {
        $simulatedTeams[$homeTeamId]->goals_for += $homeGoals;
        $simulatedTeams[$awayTeamId]->goals_for += $awayGoals;

        $simulatedTeams[$homeTeamId]->goal_difference += ($homeGoals - $awayGoals);
        $simulatedTeams[$awayTeamId]->goal_difference += ($awayGoals - $homeGoals);

        if ($homeGoals > $awayGoals) {
            $simulatedTeams[$homeTeamId]->points += 3;
        } elseif ($homeGoals < $awayGoals) {
            $simulatedTeams[$awayTeamId]->points += 3;
        } else {
            $simulatedTeams[$homeTeamId]->points += 1;
            $simulatedTeams[$awayTeamId]->points += 1;
        }
    }

    /**
     * Determine the winner of a simulation.
     *
     * @param array $simulatedTeams
     * @return int
     */
    private function determineSimulationWinner(array $simulatedTeams): int
    {
        $sortedTeams = collect($simulatedTeams)->sortBy([
            ['points', 'desc'],
            ['goal_difference', 'desc'],
            ['goals_for', 'desc'],
        ])->values();

        return $sortedTeams[0]->id;
    }
}
