<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class FixtureService
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function generateFixtures(): void
    {
        $teams = Team::all();

        if ($teams->isEmpty()) {
            $this->teamService->initializeTeams();
            $teams = Team::all();
        }

        // Clear existing fixtures
        Fixture::truncate();

        // Define our fixture structure
        $fixturesPerWeek = 6;
        $totalWeeks = 4;
        $totalFixtures = 24; // 6 fixtures per week * 4 weeks

        $teamsArray = $teams->toArray();
        $teamCount = count($teamsArray);

        // Create array of team indices (0 to teamCount-1)
        $indices = [];
        for ($i = 0; $i < $teamCount; $i++) {
            $indices[] = $i;
        }

        // Generate all possible fixtures using round-robin algorithm
        $allPossibleFixtures = [];

        // Generate fixtures for each round using round-robin tournament scheduling
        for ($round = 0; $round < $teamCount - 1; $round++) {
            // Pair teams for this round by matching first with last, second with second-last, etc.
            for ($match = 0; $match < $teamCount / 2; $match++) {
                $home = $indices[$match];
                $away = $indices[$teamCount - 1 - $match];

                // Alternate home/away to balance matches
                if ($round % 2 == 0) {
                    $allPossibleFixtures[] = [
                        'home_team_id' => $teamsArray[$home]['id'],
                        'away_team_id' => $teamsArray[$away]['id']
                    ];
                } else {
                    $allPossibleFixtures[] = [
                        'home_team_id' => $teamsArray[$away]['id'],
                        'away_team_id' => $teamsArray[$home]['id']
                    ];
                }
            }

            // Rotate teams for next round (keeping first team fixed)
            $this->rotateTeams($indices);
        }

        // Add reverse fixtures (home/away swapped)
        $fixtureCount = count($allPossibleFixtures);
        for ($i = 0; $i < $fixtureCount; $i++) {
            $allPossibleFixtures[] = [
                'home_team_id' => $allPossibleFixtures[$i]['away_team_id'],
                'away_team_id' => $allPossibleFixtures[$i]['home_team_id']
            ];
        }

        // Shuffle fixtures to create variety
        shuffle($allPossibleFixtures);

        // Distribute fixtures across weeks
        $fixtureCount = 0;
        foreach ($allPossibleFixtures as $fixture) {
            if ($fixtureCount < $totalFixtures) {
                $week = floor($fixtureCount / $fixturesPerWeek) + 1;

                Fixture::query()->create([
                    'week' => $week,
                    'home_team_id' => $fixture['home_team_id'],
                    'away_team_id' => $fixture['away_team_id'],
                    'played' => false,
                ]);

                $fixtureCount++;
            }
        }

        Log::info('Total fixtures generated: ' . Fixture::count('id'));
    }

    /**
     * Rotate teams for round-robin scheduling.
     *
     * This is a key part of the round-robin algorithm:
     * - The first element (team at index 0) stays fixed
     * - All other elements rotate clockwise
     * - This creates a different set of pairings for each round
     *
     * Let's understand with an example with 6 teams (0-5):
     * Round 1: [0,1,2,3,4,5] → Matches: 0-5, 1-4, 2-3
     * After rotation: [0,5,1,2,3,4]
     * Round 2: [0,5,1,2,3,4] → Matches: 0-4, 5-3, 1-2
     * And so on...
     *
     * @param array $teams Array of team indices to rotate
     * @return void
     */
    private function rotateTeams(array &$teams): void
    {
        if (count($teams) < 3) {
            return;
        }

        $temp = $teams[1];

        // Move all elements except first and second one position to the left
        for ($i = 1; $i < count($teams) - 1; $i++) {
            $teams[$i] = $teams[$i + 1];
        }

        $teams[count($teams) - 1] = $temp;
    }

    public function resetFixtures(): void
    {
        Fixture::query()->update([
            'played' => false,
            'home_goals' => null,
            'away_goals' => null,
        ]);
    }

    /**
     * @param int $week
     * @return Collection
     */
    public function getFixturesByWeek(int $week): Collection
    {
        return Fixture::with(['homeTeam', 'awayTeam'])
            ->where('week', $week)
            ->get();
    }
}
