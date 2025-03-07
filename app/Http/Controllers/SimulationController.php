<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Team;
use App\Services\MatchService;
use App\Services\PredictionService;
use App\Services\SimulationService;
use App\Services\SimulationStateService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SimulationController extends Controller
{
    /**
     * The simulation service instance.
     *
     * @var SimulationService
     */
    protected $simulationService;

    /**
     * The simulation state service instance.
     *
     * @var SimulationStateService
     */
    protected $stateService;

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
     * Create a new controller instance.
     *
     * @param SimulationService $simulationService
     * @param SimulationStateService $stateService
     * @param MatchService $matchService
     * @param PredictionService $predictionService
     */
    public function __construct(
        SimulationService      $simulationService,
        SimulationStateService $stateService,
        MatchService           $matchService,
        PredictionService      $predictionService
    )
    {
        $this->simulationService = $simulationService;
        $this->stateService = $stateService;
        $this->matchService = $matchService;
        $this->predictionService = $predictionService;
    }

    /**
     * Display the simulation dashboard.
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        $state = $this->stateService->getCurrentState();
        $teams = Team::all()->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();

        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get();

        return Inertia::render('Dashboard', [
            'teams' => $teams,
            'fixtures' => $fixtures,
            'currentWeek' => $state->current_week,
            'isSimulationComplete' => $state->is_simulation_complete,
        ]);
    }

    /**
     * Get the current state of the simulation.
     *
     * @return JsonResponse
     */
    public function getState(): JsonResponse
    {
        $state = $this->stateService->getCurrentState();

        return response()->json([
            'current_week' => $state->current_week,
            'is_simulation_complete' => $state->is_simulation_complete,
        ]);
    }

    /**
     * Simulate a single match.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function simulateMatch(int $id): JsonResponse
    {
        $match = $this->matchService->simulateMatch($id);

        if (!$match) {
            return response()->json([
                'message' => 'Match not found or already played',
            ], 404);
        }

        return response()->json([
            'message' => 'Match simulated successfully',
            'match' => $match->load(['homeTeam', 'awayTeam']),
        ]);
    }

    /**
     * Simulate all matches for the next week.
     *
     * @return JsonResponse
     */
    public function simulateNextWeek(): JsonResponse
    {
        $result = $this->simulationService->simulateNextWeek();

        if (isset($result['message'])) {
            return response()->json([
                'message' => $result['message'],
            ]);
        }

        $teams = Team::all()->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();

        return response()->json([
            'message' => 'Week ' . $result['week'] . ' simulated successfully',
            'week' => $result['week'],
            'matches' => $result['matches'],
            'teams' => $teams,
            'is_complete' => $result['is_complete'],
        ]);
    }

    /**
     * Simulate all remaining weeks.
     *
     * @return JsonResponse
     */
    public function simulateAllWeeks(): JsonResponse
    {
        $result = $this->simulationService->simulateAllWeeks();

        $teams = Team::all()->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();

        return response()->json([
            'message' => 'All weeks simulated successfully',
            'results' => $result['results'],
            'teams' => $teams,
            'is_complete' => $result['is_complete'],
        ]);
    }

    /**
     * Reset the simulation.
     *
     * @return JsonResponse
     */
    public function resetSimulation(): JsonResponse
    {
        $this->simulationService->resetSimulation();

        $teams = Team::all();
        $fixtures = Fixture::all();
        $state = $this->stateService->getCurrentState();

        return response()->json([
            'message' => 'Simulation reset successfully',
            'teams' => $teams,
            'fixtures' => $fixtures,
            'current_week' => $state->current_week,
            'is_simulation_complete' => $state->is_simulation_complete,
        ]);
    }

    /**
     * Update championship predictions.
     *
     * @return JsonResponse
     */
    public function updatePredictions(): JsonResponse
    {
        $this->predictionService->updatePredictions();

        $teams = Team::all()->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();

        return response()->json([
            'message' => 'Predictions updated successfully',
            'teams' => $teams,
        ]);
    }
}
