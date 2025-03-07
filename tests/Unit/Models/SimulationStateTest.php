<?php

namespace Tests\Unit\Models;

use App\Models\SimulationState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_simulation_state(): void
    {
        $state = SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $this->assertInstanceOf(SimulationState::class, $state);
        $this->assertEquals(0, $state->current_week);
        $this->assertFalse($state->is_simulation_complete);
    }

    public function test_simulation_state_update(): void
    {
        $state = SimulationState::query()->create([
            'current_week' => 0,
            'is_simulation_complete' => false,
        ]);

        $state->current_week = 3;
        $state->is_simulation_complete = true;
        $state->save();

        $state = $state->fresh();

        $this->assertEquals(3, $state->current_week);
        $this->assertTrue($state->is_simulation_complete);
    }

    public function test_get_current_state_method(): void
    {
        $state = SimulationState::getCurrentState();

        $this->assertInstanceOf(SimulationState::class, $state);
        $this->assertEquals(0, $state->current_week);
        $this->assertFalse($state->is_simulation_complete);

        $state->current_week = 2;
        $state->save();

        $state = SimulationState::getCurrentState();

        $this->assertEquals(2, $state->current_week);
        $this->assertFalse($state->is_simulation_complete);
    }
}
