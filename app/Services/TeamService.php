<?php

namespace App\Services;

use App\Models\Team;

class TeamService
{
    public function initializeTeams(): void
    {
        Team::truncate();

        $teams = [
            [
                'name' => 'Manchester City',
                'power' => 90,
            ],
            [
                'name' => 'Liverpool',
                'power' => 85,
            ],
            [
                'name' => 'Arsenal',
                'power' => 80,
            ],
            [
                'name' => 'Chelsea',
                'power' => 75,
            ],
        ];

        foreach ($teams as $team) {
            Team::query()->create($team);
        }
    }

    public function applyStats($match, $isResetting = false): void
    {
        $this->updateTeamStats(
            homeTeamId: $match->home_team_id,
            awayTeamId: $match->away_team_id,
            homeGoals: $match->home_goals,
            awayGoals: $match->away_goals,
            isReset: $isResetting
        );
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
    public function updateTeamStats($homeTeamId, $awayTeamId, $homeGoals, $awayGoals, $isReset = false): void
    {
        $homeTeam = Team::query()->find($homeTeamId);
        $awayTeam = Team::query()->find($awayTeamId);

        if (!$homeTeam || !$awayTeam) {
            return;
        }

        $multiplier = $isReset ? -1 : 1;

        // Update home team stats
        $homeTeam->played += 1 * $multiplier;
        $homeTeam->won += ($homeGoals > $awayGoals) ? 1 * $multiplier : 0;
        $homeTeam->drawn += ($homeGoals === $awayGoals) ? 1 * $multiplier : 0;
        $homeTeam->lost += ($homeGoals < $awayGoals) ? 1 * $multiplier : 0;
        $homeTeam->goals_for += $homeGoals * $multiplier;
        $homeTeam->goals_against += $awayGoals * $multiplier;
        $homeTeam->goal_difference = $homeTeam->goals_for - $homeTeam->goals_against;
        $homeTeam->points = $homeTeam->won * 3 + $homeTeam->drawn;
        $homeTeam->save();

        // Update away team stats
        $awayTeam->played += 1 * $multiplier;
        $awayTeam->won += ($awayGoals > $homeGoals) ? 1 * $multiplier : 0;
        $awayTeam->drawn += ($homeGoals === $awayGoals) ? 1 * $multiplier : 0;
        $awayTeam->lost += ($awayGoals < $homeGoals) ? 1 * $multiplier : 0;
        $awayTeam->goals_for += $awayGoals * $multiplier;
        $awayTeam->goals_against += $homeGoals * $multiplier;
        $awayTeam->goal_difference = $awayTeam->goals_for - $awayTeam->goals_against;
        $awayTeam->points = $awayTeam->won * 3 + $awayTeam->drawn;
        $awayTeam->save();
    }

    /**
     * Reset all team statistics.
     *
     * @return void
     */
    public function resetTeamStats(): void
    {
        Team::query()->update([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'win_probability' => 0,
        ]);
    }
}
