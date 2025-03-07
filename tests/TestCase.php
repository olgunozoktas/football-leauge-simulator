<?php

namespace Tests;

use App\Models\SimulationState;
use App\Models\User;
use App\Services\SimulationService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    /**
     * Create a user for testing.
     *
     * @param array $attributes
     * @return User
     */
    protected function createUser($attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Initialize the league with teams and fixtures.
     *
     * @return void
     */
    protected function initializeLeague(): void
    {
        $simulationService = app(SimulationService::class);
        $simulationService->initializeTeams();
        $simulationService->generateFixtures();

        SimulationState::create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);
    }
}
