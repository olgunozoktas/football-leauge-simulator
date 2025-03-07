<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\SimulationState;

class SimulationStateService
{
    /**
     * Get the current simulation state.
     *
     * @return SimulationState
     */
    public function getCurrentState()
    {
        return SimulationState::getCurrentState();
    }

    /**
     * Update the current week.
     *
     * @param int $week
     * @return SimulationState
     */
    public function updateCurrentWeek(int $week): SimulationState
    {
        $state = $this->getCurrentState();
        $state->current_week = $week;

        $maxWeek = Fixture::max('week');

        if ($week === $maxWeek) {
            $state->is_simulation_complete = true;
        }

        $state->save();

        return $state;
    }

    /**
     * Reset the simulation state.
     *
     * @return SimulationState
     */
    public function resetState()
    {
        $state = $this->getCurrentState();
        $state->current_week = 0;
        $state->is_simulation_complete = false;
        $state->save();

        return $state;
    }

    /**
     * Mark the simulation as complete.
     *
     * @return SimulationState
     */
    public function completeSimulation()
    {
        $state = $this->getCurrentState();

        // Get the maximum week from fixtures
        $maxWeek = Fixture::max('week');

        $state->current_week = $maxWeek;
        $state->is_simulation_complete = true;
        $state->save();

        return $state;
    }
}
