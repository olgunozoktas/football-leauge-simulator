<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Services\FixtureService;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    /**
     * @var FixtureService
     */
    protected $fixtureService;

    /**
     * @var MatchService
     */
    protected $matchService;

    /**
     * @param FixtureService $fixtureService
     * @param MatchService $matchService
     */
    public function __construct(
        FixtureService $fixtureService,
        MatchService   $matchService
    )
    {
        $this->fixtureService = $fixtureService;
        $this->matchService = $matchService;
    }

    public function index(): JsonResponse
    {
        $fixtures = Fixture::query()
            ->with(['homeTeam', 'awayTeam'])->get();

        return response()->json($fixtures);
    }

    /**
     * Display fixtures for a specific week.
     *
     * @param int $week
     * @return JsonResponse
     */
    public function getByWeek(int $week): JsonResponse
    {
        $fixtures = $this->fixtureService->getFixturesByWeek($week);

        return response()->json($fixtures);
    }

    /**
     * Generate fixtures for the teams.
     *
     * @return JsonResponse
     */
    public function generate(): JsonResponse
    {
        $this->fixtureService->generateFixtures();
        $fixtures = Fixture::query()
            ->with(['homeTeam', 'awayTeam'])->get();

        return response()->json([
            'message' => 'Fixtures generated successfully',
            'fixtures' => $fixtures,
        ]);
    }

    /**
     * Update a match result manually.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateResult(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'home_goals' => 'required|integer|min:0',
            'away_goals' => 'required|integer|min:0',
        ]);

        $match = $this->matchService->updateMatchResult(
            $id,
            $validated['home_goals'],
            $validated['away_goals']
        );

        if (!$match) {
            return response()->json([
                'message' => 'Match not found or not played yet',
            ], 404);
        }

        return response()->json([
            'message' => 'Match result updated successfully',
            'match' => $match->load(['homeTeam', 'awayTeam']),
        ]);
    }
}
