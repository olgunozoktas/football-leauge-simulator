<?php

namespace App\Console\Commands;

use App\Services\SimulationService;
use Illuminate\Console\Command;

class ResetLeague extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the football league simulation';

    /**
     * The simulation service instance.
     *
     * @var SimulationService
     */
    protected $simulationService;

    /**
     * Create a new command instance.
     *
     * @param SimulationService $simulationService
     * @return void
     */
    public function __construct(SimulationService $simulationService)
    {
        parent::__construct();
        $this->simulationService = $simulationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Resetting league simulation...');
        $this->simulationService->resetSimulation();

        $this->info('League simulation reset successfully!');

        return Command::SUCCESS;
    }
}
