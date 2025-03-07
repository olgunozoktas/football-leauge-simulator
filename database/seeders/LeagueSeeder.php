<?php

namespace Database\Seeders;

use App\Models\SimulationState;
use App\Services\SimulationService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $simulationService = app(SimulationService::class);
        $simulationService->initializeTeams();
        $simulationService->generateFixtures();
    }
}
