<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * The team service instance.
     *
     * @var TeamService
     */
    protected TeamService $teamService;

    /**
     * Create a new controller instance.
     *
     * @param TeamService $teamService
     * @return void
     */
    public function __construct(
        TeamService $teamService
    ) {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the teams.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $teams = Team::all()->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();

        return response()->json($teams);
    }

    /**
     * Initialize teams with default values.
     *
     * @return JsonResponse
     */
    public function initialize(): JsonResponse
    {
        $this->teamService->initializeTeams();
        $teams = Team::all();

        return response()->json([
            'message' => 'Teams initialized successfully',
            'teams' => $teams,
        ]);
    }

    /**
     * Update team information.
     *
     * @param Request $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $team = Team::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'power' => 'sometimes|integer|min:1|max:100',
        ]);

        $team->update($validated);

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team,
        ]);
    }
}
