<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationState extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'simulation_state';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'current_week',
        'is_simulation_complete',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_simulation_complete' => 'boolean',
    ];

    /**
     * Get the current simulation state.
     *
     * @return SimulationState
     */
    public static function getCurrentState(): SimulationState
    {
        $state = self::query()->first();

        if (!$state) {
            $state = self::query()->create([
                'current_week' => 0,
                'is_simulation_complete' => false,
            ]);
        }

        return $state;
    }
}
